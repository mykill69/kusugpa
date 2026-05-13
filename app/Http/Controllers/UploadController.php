<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Summary;
use App\Models\TruckingAllowance;
use App\Models\FuelAllowance;
use App\Models\RentalAllowance;
use App\Models\UnderloadAllowance;
use App\Models\TransloadingAllowance;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Add this import

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
        ];

        $requiredPermission = $permissionMap[$type] ?? null;

        // Check if user has permission
        if ($requiredPermission) {
            /** @var User $user */
            $user = auth()->user();
            
            // Administrator and super_admin always have access
            if ($user->role !== 'Administrator' && $user->role !== 'super_admin') {
                // Check if user has the specific permission
                $hasPermission = $user->permissions()
                    ->where('slug', $requiredPermission)
                    ->exists();
                
                if (!$hasPermission) {
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
            $lines = array_map('str_getcsv', explode("\n", $csvData));
            
            $batchData = [];
            $errors = [];
            $userId = auth()->id();
            
            foreach ($lines as $index => $line) {
                // Skip empty lines
                if (empty(array_filter($line))) continue;
                
                $method = 'process' . ucfirst($type) . 'Line';
                
                if (method_exists($this, $method)) {
                    $processed = $this->$method($line, $index, $userId);
                    
                    if ($processed['success']) {
                        $batchData[] = $processed['data'];
                    } else {
                        $errors[] = $processed['error'];
                    }
                } else {
                    return response()->json(['error' => 'Invalid upload type'], 400);
                }
            }
            
            // Batch insert for better performance
            if (!empty($batchData)) {
                $model = $this->getModel($type);
                $model::insert($batchData);
            }
            
            $message = "Successfully imported " . count($batchData) . " records.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " records skipped due to errors.";
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('CSV Upload Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to upload CSV: ' . $e->getMessage());
        }
    }
    
    private function processSummaryLine($line, $index, $userId)
    {
        if (count($line) < 6) {
            return [
                'success' => false,
                'error' => "Line {$index}: Insufficient columns"
            ];
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
        if (count($line) < 5) {
            return [
                'success' => false,
                'error' => "Line {$index}: Insufficient columns"
            ];
        }
        
        return [
            'success' => true,
            'data' => [
                'crop_year' => (int) preg_replace('/[^0-9]/', '', trim($line[0])),
                'week_no' => (int) $line[1],
                'planter_code' => (int) $line[2],
                'planter_name' => trim($line[3]),
                'trucking_amount' => (float) str_replace(',', '', $line[4]),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
    }
    
    private function processFuelLine($line, $index, $userId)
    {
        if (count($line) < 5) {
            return [
                'success' => false,
                'error' => "Line {$index}: Insufficient columns"
            ];
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
            return [
                'success' => false,
                'error' => "Line {$index}: Insufficient columns"
            ];
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
            return [
                'success' => false,
                'error' => "Line {$index}: Insufficient columns"
            ];
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
            return [
                'success' => false,
                'error' => "Line {$index}: Insufficient columns"
            ];
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
            return [
                'success' => false,
                'error' => "Line {$index}: Insufficient columns. Expected 7 columns."
            ];
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
            'mudpress' => \App\Models\Mudpress::class, // Add this
        ];
        
        if (!isset($models[$type])) {
            throw new \InvalidArgumentException("Invalid upload type: {$type}");
        }
        
        return $models[$type];
    }
}