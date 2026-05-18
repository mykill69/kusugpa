<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Summary;
use App\Models\TruckingAllowance;
use App\Models\FuelAllowance;
use App\Models\RentalAllowance;
use App\Models\UnderloadAllowance;
use App\Models\TransloadingAllowance;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UploadController extends Controller
{
    public function uploadCSV(Request $request, $type)
    {
        $permissionMap = [
            'summary' => 'upload-summary',
            'trucking' => 'upload-trucking',
            'fuel' => 'upload-fuel',
            'rentals' => 'upload-rentals',
            'underload' => 'upload-underload',
            'transloading' => 'upload-transloading',
            'fci' => 'upload-fci',
            'mudpress' => 'upload-mudpress',
            'consolidated' => 'upload-consolidated',
            'quedan' => 'upload-quedan',
            'molasses' => 'upload-molasses',
        ];

        $requiredPermission = $permissionMap[$type] ?? null;

        if ($requiredPermission) {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            
            if ($user->role !== 'Administrator' && $user->role !== 'super_admin') {
                $hasPermission = $user->permissions()
                    ->where('slug', $requiredPermission)
                    ->exists();
                
                if (!$hasPermission) {
                    AuditLog::log('error', 'uploads', 'Unauthorized upload attempt: ' . $type);
                    return redirect()->back()
                        ->with('error', 'You do not have permission to upload ' . $type . ' files.');
                }
            }
        }

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $csvData = file_get_contents($file);
            
            // Auto-detect delimiter
            $delimiter = str_contains($csvData, "\t") ? "\t" : ',';
            
            Log::info('CSV Upload started', [
                'type' => $type,
                'delimiter' => $delimiter === "\t" ? 'tab' : 'comma',
                'file_size' => strlen($csvData)
            ]);
            
            // Parse CSV
            $rows = explode("\n", trim($csvData));
            $lines = [];
            foreach ($rows as $row) {
                if (empty(trim($row))) continue;
                $lines[] = str_getcsv($row, $delimiter);
            }
            
            Log::info('CSV Lines parsed', ['total_lines' => count($lines)]);
            
            if (!empty($lines)) {
                Log::info('First data line', ['line' => $lines[0] ?? 'N/A', 'columns' => count($lines[0] ?? [])]);
            }
            
            $batchData = [];
            $errors = [];
            $userId = auth()->id();
            
            foreach ($lines as $index => $line) {
                if (empty(array_filter($line))) continue;
                
                if ($index === 0 && !is_numeric(trim($line[0]))) {
                    Log::info('Skipping header row', ['row' => $line]);
                    continue;
                }
                
                $method = 'process' . ucfirst($type) . 'Line';
                
                if (method_exists($this, $method)) {
                    $processed = $this->$method($line, $index, $userId);
                    
                    if ($processed['success']) {
                        $batchData[] = $processed['data'];
                    } else {
                        $errors[] = $processed['error'];
                        Log::warning('CSV line error', ['line' => $index, 'error' => $processed['error']]);
                    }
                }
            }
            
            Log::info('CSV Processing complete', [
                'records_to_insert' => count($batchData),
                'errors' => count($errors)
            ]);
            
            $importedCount = 0;
            if (!empty($batchData)) {
                $model = $this->getModel($type);
                Log::info('Inserting into model', ['model' => $model, 'first_record' => $batchData[0]]);
                
                foreach (array_chunk($batchData, 100) as $chunk) {
                    try {
                        $model::insert($chunk);
                        $importedCount += count($chunk);
                        Log::info('Chunk inserted', ['count' => count($chunk)]);
                    } catch (\Exception $e) {
                        Log::error('Bulk insert failed, trying individual records', ['error' => $e->getMessage()]);
                        // Try inserting one by one
                        foreach ($chunk as $record) {
                            try {
                                $model::create($record);
                                $importedCount++;
                            } catch (\Exception $e2) {
                                Log::warning('Row insert failed', [
                                    'planter' => $record['planter_name'] ?? 'unknown',
                                    'error' => $e2->getMessage()
                                ]);
                                $errors[] = 'Skipped: ' . ($record['planter_name'] ?? 'unknown');
                            }
                        }
                    }
                }
            }
            
            // Log successful upload to audit trail
            AuditLog::log('upload', 'uploads', 'Uploaded ' . $type . ' CSV: ' . $importedCount . ' records imported', [
                'type' => $type,
                'file_name' => $file->getClientOriginalName(),
                'records_imported' => $importedCount,
                'records_skipped' => count($errors),
                'total_rows' => count($lines),
            ]);
            
            $message = "Successfully imported " . $importedCount . " records.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " records skipped.";
            }
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'imported' => $importedCount,
                    'errors' => count($errors)
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('CSV Upload Error (' . $type . '): ' . $e->getMessage());
            
            AuditLog::log('error', 'uploads', 'CSV upload failed: ' . $type, [
                'error' => $e->getMessage(),
                'file_name' => $file->getClientOriginalName() ?? 'unknown',
            ]);
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to upload CSV: ' . $e->getMessage());
        }
    }
    
    private function processSummaryLine($line, $index, $userId)
    {
        if (count($line) < 6) {
            return ['success' => false, 'error' => "Line {$index}: Insufficient columns"];
        }
        
        return [
            'success' => true,
            'data' => [
                'crop_year' => (int) preg_replace('/[^0-9]/', '', trim($line[0])),
                'week_no' => (int) $line[1],
                'planter_code' => (int) $line[2],
                'planter_name' => trim($line[3]),
                'net_cane' => (float) str_replace(',', '', $line[4]),
                'net_amount' => (float) str_replace(',', '', $line[5]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
    }
    
    private function processTruckingLine($line, $index, $userId)
    {
        if (count($line) < 7) {
            return ['success' => false, 'error' => "Line {$index}: Expected 7 columns, got " . count($line)];
        }

        $planterName = trim($line[3]);
        $planterName = mb_convert_encoding($planterName, 'UTF-8', 'UTF-8');
        $planterName = preg_replace('/[^\x20-\x7E\xA0-\xFF]/', '', $planterName);

        return [
            'success' => true,
            'data' => [
                'crop_year' => trim($line[0]),
                'week_no' => trim($line[1]),
                'planter_code' => trim($line[2]),
                'planter_name' => $planterName,
                'net_cane' => (float) str_replace(',', '', trim($line[4])),
                'ta_amount' => (float) str_replace(',', '', trim($line[5])),
                'trans_code' => trim($line[6]),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
    }
    
    private function processFuelLine($line, $index, $userId)
    {
        if (count($line) < 5) {
            return ['success' => false, 'error' => "Line {$index}: Insufficient columns"];
        }
        
        return [
            'success' => true,
            'data' => [
                'crop_year' => (int) preg_replace('/[^0-9]/', '', trim($line[0])),
                'week_no' => (int) $line[1],
                'planter_code' => (int) $line[2],
                'planter_name' => trim($line[3]),
                'fuel_amount' => (float) str_replace(',', '', $line[4]),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
    }
    
    private function processRentalLine($line, $index, $userId)
    {
        if (count($line) < 5) {
            return ['success' => false, 'error' => "Line {$index}: Insufficient columns"];
        }
        
        return [
            'success' => true,
            'data' => [
                'crop_year' => (int) preg_replace('/[^0-9]/', '', trim($line[0])),
                'week_no' => (int) $line[1],
                'planter_code' => (int) $line[2],
                'planter_name' => trim($line[3]),
                'rental_amount' => (float) str_replace(',', '', $line[4]),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
    }
    
    private function processUnderloadLine($line, $index, $userId)
    {
        if (count($line) < 5) {
            return ['success' => false, 'error' => "Line {$index}: Insufficient columns"];
        }
        
        return [
            'success' => true,
            'data' => [
                'crop_year' => (int) preg_replace('/[^0-9]/', '', trim($line[0])),
                'week_no' => (int) $line[1],
                'planter_code' => (int) $line[2],
                'planter_name' => trim($line[3]),
                'underload_amount' => (float) str_replace(',', '', $line[4]),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
    }
    
    private function processTransloadingLine($line, $index, $userId)
    {
        if (count($line) < 5) {
            return ['success' => false, 'error' => "Line {$index}: Insufficient columns"];
        }
        
        return [
            'success' => true,
            'data' => [
                'crop_year' => (int) preg_replace('/[^0-9]/', '', trim($line[0])),
                'week_no' => (int) $line[1],
                'planter_code' => (int) $line[2],
                'planter_name' => trim($line[3]),
                'transloading_amount' => (float) str_replace(',', '', $line[4]),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
    }

    private function processMudpressLine($line, $index, $userId)
    {
        if (count($line) < 7) {
            return ['success' => false, 'error' => "Line {$index}: Insufficient columns. Expected 7 columns."];
        }

        return [
            'success' => true,
            'data' => [
                'crop_year' => trim($line[0]),
                'week_no' => trim($line[1]),
                'planter_code' => trim($line[2]),
                'planter_name' => trim($line[3]),
                'trans_code' => trim($line[4]),
                'charge_code' => trim($line[5]),
                'mpress' => (float) str_replace(',', '', $line[6]),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
    }
    
    private function getModel($type)
    {
        $models = [
            'summary' => \App\Models\Summary::class,
            'trucking' => \App\Models\TruckingAllowance::class,
            'fuel' => \App\Models\FuelAllowance::class,
            'rentals' => \App\Models\RentalAllowance::class,
            'underload' => \App\Models\UnderloadAllowance::class,
            'transloading' => \App\Models\TransloadingAllowance::class,
            'fci' => \App\Models\FreshCaneIncentive::class,
            'mudpress' => \App\Models\Mudpress::class,
            'consolidated' => \App\Models\ConsolidatedUpload::class,
            'quedan' => \App\Models\Quedan::class,
            'molasses' => \App\Models\Molass::class,
        ];
        
        if (!isset($models[$type])) {
            throw new \InvalidArgumentException("Invalid upload type: {$type}");
        }
        
        return $models[$type];
    }

    private function processConsolidatedLine($line, $index, $userId)
{
    if (count($line) < 21) {
        return ['success' => false, 'error' => "Line {$index}: Expected 21 columns, got " . count($line)];
    }

    $planterName = trim($line[2]);
    $assnName = trim($line[3]);
    $planterName = mb_convert_encoding($planterName, 'UTF-8', 'UTF-8');
    $planterName = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $planterName);
    $assnName = mb_convert_encoding($assnName, 'UTF-8', 'UTF-8');
    $assnName = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $assnName);

    $taAmount = (float) str_replace(',', '', trim($line[5]));
    $cciFaAmt = (float) str_replace(',', '', trim($line[11]));
    $cciFbAmt = (float) str_replace(',', '', trim($line[13]));
    $cciFcAmt = (float) str_replace(',', '', trim($line[15]));
    $mudpressAmt = (float) str_replace(',', '', trim($line[19]));
    $fuelAmt = (float) str_replace(',', '', trim($line[16]));
    $rentalAmt = (float) str_replace(',', '', trim($line[17]));
    $underloadAmt = (float) str_replace(',', '', trim($line[18]));

    // Compute total_summary
    $totalSummary = $taAmount + $cciFaAmt + $cciFbAmt + $cciFcAmt + $mudpressAmt - $fuelAmt - $rentalAmt - $underloadAmt;

    return [
        'success' => true,
        'data' => [
            'planter_code' => trim($line[0]),
            'assn_code' => trim($line[1]),
            'planter_name' => $planterName,
            'assn_name' => $assnName,
            'ta_wt' => (float) str_replace(',', '', trim($line[4])),
            'ta_amount' => $taAmount,
            'emi_wt' => (float) str_replace(',', '', trim($line[6])),
            'emi_amount' => (float) str_replace(',', '', trim($line[7])),
            'pat_wt' => (float) str_replace(',', '', trim($line[8])),
            'pat_amount' => (float) str_replace(',', '', trim($line[9])),
            'cci_fa_wt' => (float) str_replace(',', '', trim($line[10])),
            'cci_fa_amt' => $cciFaAmt,
            'cci_fb_wt' => (float) str_replace(',', '', trim($line[12])),
            'cci_fb_amt' => $cciFbAmt,
            'cci_fc_wt' => (float) str_replace(',', '', trim($line[14])),
            'cci_fc_amt' => $cciFcAmt,
            'fuel_issuance_amt' => $fuelAmt,
            'rental_amt' => $rentalAmt,
            'underload_amt' => $underloadAmt,
            'mudpress_amt' => $mudpressAmt,
            'adj_amt' => (float) str_replace(',', '', trim($line[20])),
            'total_summary' => $totalSummary,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ];
}


private function processQuedanLine($line, $index, $userId)
{
    if (count($line) < 9) {
        return ['success' => false, 'error' => "Line {$index}: Expected 9 columns, got " . count($line)];
    }

    $planterName = trim($line[3]);
    $planterName = mb_convert_encoding($planterName, 'UTF-8', 'UTF-8');
    $planterName = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $planterName);

    return [
        'success' => true,
        'data' => [
            'crop_year' => trim($line[0]),
            'week_no' => trim($line[1]),
            'planter_code' => trim($line[2]),
            'planter_name' => $planterName,
            'qdn_no' => trim($line[4]),
            'tin_no' => trim($line[5]),
            'total_liens' => (float) str_replace(',', '', trim($line[6])),
            'sugar_lkg' => (float) str_replace(',', '', trim($line[7])),
            'labor_lkg' => (float) str_replace(',', '', trim($line[8])),
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ];
}

private function processMolassesLine($line, $index, $userId)
{
    if (count($line) < 7) {
        return ['success' => false, 'error' => "Line {$index}: Expected 7 columns, got " . count($line)];
    }

    $planterName = trim($line[3]);
    $planterName = mb_convert_encoding($planterName, 'UTF-8', 'UTF-8');
    $planterName = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $planterName);

    return [
        'success' => true,
        'data' => [
            'crop_year' => trim($line[0]),
            'week_no' => trim($line[1]),
            'planter_code' => trim($line[2]),
            'planter_name' => $planterName,
            'tin_no' => trim($line[4]),
            'mc_no' => trim($line[5]),
            'mol_net' => (float) str_replace(',', '', trim($line[6])),
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ];
}
}