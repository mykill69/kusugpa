<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CropYear;
use App\Models\WeekNo;
use App\Models\Summary;
use App\Models\QuedanPrice;
use App\Models\MolassesPrice;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\LoanSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{

    public function adminDashboard()
{
    $stats = [
        'totalUsers' => User::count(),
        'totalPlanters' => Summary::distinct('planter_code')->count(),
        'totalRecords' => Summary::count(),
        'latestBackup' => 'Never',
    ];
    
    return view('menu.admin-dashboard', compact('stats'));
}

public function adminSettings()
{
    return view('menu.admin-settings');
}
public function getSettingsData()
{
    $settings = SystemSetting::getAllSettings();
    return response()->json($settings);
}

public function updateSetting(Request $request)
{
    $request->validate([
        'key' => 'required|string',
        'value' => 'required',
    ]);

    SystemSetting::set($request->key, $request->value);
    
    Log::info('Setting updated: ' . $request->key . ' by user: ' . auth()->user()->username);
    
    return response()->json(['message' => 'Setting updated successfully']);
}

public function systemInfo()
{
    $info = [
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'database' => DB::connection()->getDatabaseName(),
        'storage' => disk_free_space(storage_path()) / 1024 / 1024 . ' MB free',
    ];
    
    return view('menu.system-info', compact('info'));
}

public function toggleMaintenance(Request $request)
{
    // Toggle maintenance mode logic
    return response()->json(['message' => 'Maintenance mode updated']);
}

public function clearCache(Request $request)
{
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    
    return response()->json(['message' => 'All caches cleared successfully']);
}

public function createBackup(Request $request)
{
    Artisan::call('backup:run');
    return response()->json(['message' => 'Backup created successfully']);
}

    public function dashboard()
    {
        return view('menu.dashboard');
    }

    public function summaryReport(Request $request)
{
    $perPage = $request->get('per_page', 50);
    
    $summaries = Summary::select('id', 'crop_year', 'week_no', 'planter_code', 'planter_name', 'net_cane', 'net_amount')
        ->orderBy('id', 'desc')
        ->paginate($perPage)
        ->withQueryString();
    
    $cropYears = CropYear::orderBy('crop_year')->pluck('crop_year');
    // Don't pass all weeks initially - they'll be loaded dynamically
    
    return view('menu.summaryReport', compact('summaries', 'cropYears'));
}

public function summaryReportData(Request $request)
{
    $cropYear = $request->get('crop_year');
    $weekFrom = $request->get('week_from');
    $weekTo = $request->get('week_to');
    $search = $request->get('search');
    $perPage = $request->get('per_page', 50);
    $sortField = $request->get('sort_field', 'id');
    $sortDirection = $request->get('sort_direction', 'desc');
    
    // Whitelist allowed sort fields to prevent SQL injection
    $allowedSortFields = ['id', 'crop_year', 'week_no', 'planter_code', 'planter_name', 'net_cane', 'net_amount'];
    if (!in_array($sortField, $allowedSortFields)) {
        $sortField = 'id';
    }
    if (!in_array($sortDirection, ['asc', 'desc'])) {
        $sortDirection = 'desc';
    }
    
    $query = Summary::select('id', 'crop_year', 'week_no', 'planter_code', 'planter_name', 'net_cane', 'net_amount');
    
    // Apply filters
    if ($cropYear) {
        $query->where('crop_year', $cropYear);
    }
    if ($weekFrom && $weekTo) {
        $query->whereBetween('week_no', [$weekFrom, $weekTo]);
    } elseif ($weekFrom) {
        $query->where('week_no', '>=', $weekFrom);
    } elseif ($weekTo) {
        $query->where('week_no', '<=', $weekTo);
    }
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('planter_name', 'LIKE', "%{$search}%")
              ->orWhere('planter_code', 'LIKE', "%{$search}%")
              ->orWhere('crop_year', 'LIKE', "%{$search}%");
        });
    }
    
    $summaries = $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    
    return response()->json([
        'data' => $summaries->items(),
        'total' => $summaries->total(),
        'current_page' => $summaries->currentPage(),
        'last_page' => $summaries->lastPage(),
        'per_page' => $summaries->perPage(),
    ]);
}

public function getWeeksByCropYear(Request $request)
{
    $cropYear = $request->get('crop_year');
    
    if (!$cropYear) {
        return response()->json(['weeks' => []]);
    }
    
    // Get unique week numbers from the summary table for the selected crop year
    $weeks = Summary::where('crop_year', $cropYear)
        ->select('week_no')
        ->distinct()
        ->orderBy('week_no')
        ->pluck('week_no');
    
    return response()->json(['weeks' => $weeks]);
}

    public function previewPDF(Request $request)
    {
        $cropYear = $request->input('crop_year');
        $weekFrom = $request->input('week_from');
        $weekTo = $request->input('week_to');

        $query = Summary::where('crop_year', $cropYear);

        if ($weekFrom && $weekTo) {
            $query->whereBetween('week_no', [$weekFrom, $weekTo]);
        }

        $summaries = $query->orderBy('week_no')->take(500)->get();

        $pdf = Pdf::loadView('modal.viewSummaryPdf', compact('summaries', 'cropYear', 'weekFrom', 'weekTo'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('summary-report.pdf');
    }

    public function downloadPDF(Request $request)
    {
        $cropYear = $request->input('crop_year');
        $weekFrom = $request->input('week_from');
        $weekTo = $request->input('week_to');

        $query = Summary::where('crop_year', $cropYear);

        if ($weekFrom && $weekTo) {
            $query->whereBetween('week_no', [$weekFrom, $weekTo]);
        }

        $summaries = $query->orderBy('week_no')->take(500)->get();

        $pdf = Pdf::loadView('modal.viewSummaryPdf', compact('summaries', 'cropYear', 'weekFrom', 'weekTo'))
                    ->setPaper('a4', 'portrait');

        return $pdf->download('summary-report.pdf');
    }

    public function printVoucher(Request $request)
    {
        $cropYear = CropYear::pluck('crop_year');
        $weekNos = WeekNo::pluck('week_no')->sort();
        $selectedCropYear = $request->input('crop_year');
        $weekFrom = $request->input('week_from');
        $weekTo = $request->input('week_to');
        $selectedPlanters = $request->input('planter_name');

        $planterNames = Summary::select('planter_name')->distinct()->pluck('planter_name');

        return view('menu.printVoucher', compact(
            'cropYear',
            'weekNos',
            'selectedCropYear',
            'weekFrom',
            'weekTo',
            'planterNames',
            'selectedPlanters'
        ));
    }

    // In MenuController.php - Update voucherPDF method
public function voucherPDF(Request $request)
{
    $cropYear = $request->input('crop_year');
    $weekFrom = $request->input('week_from');
    $weekTo = $request->input('week_to');
    $planterNames = $request->input('planter_name');

    $query = Summary::where('summary.crop_year', $cropYear)
        ->whereBetween('summary.week_no', [$weekFrom, $weekTo]);

    if (!empty($planterNames)) {
        $query->whereIn('summary.planter_name', (array) $planterNames);
    }

    $summaryData = $query->leftJoin('quedan_price', function ($join) {
            $join->on('summary.crop_year', '=', 'quedan_price.crop_year')
                ->on('summary.week_no', '=', 'quedan_price.week_no');
        })
        ->select('summary.*', 'quedan_price.quedan_price as qp')
        ->get();

    // Add loan deductions
    $autoDeduct = LoanSetting::get('auto_deduct', true);
    $loanController = new LoanController();
    
    foreach ($summaryData as $summary) {
        $summary->loan_deduction = 0;
        $summary->net_proceeds = $summary->net_amount;
        
        if ($autoDeduct) {
            $deduction = $loanController->getActiveDeductions(
                $summary->planter_code, 
                $cropYear, 
                $summary->week_no
            );
            $summary->loan_deduction = $deduction;
            $summary->net_proceeds = $summary->net_amount - $deduction;
        }
    }

    return Pdf::loadView('pdf.voucher', compact('summaryData', 'autoDeduct'))->stream('voucher-preview.pdf');
}

    public function dashboardData(Request $request)
{
    // Get filter parameters from request
    $filterCropYear = $request->get('crop_year');
    $filterWeekNo = $request->get('week_no');
    
    $allCropYears = CropYear::orderBy('crop_year')->pluck('crop_year');
    
    // Use filtered crop year or default
    $currentCropYear = $filterCropYear ?? Summary::orderBy('crop_year', 'desc')->value('crop_year');
    
    if (!$currentCropYear) {
        $currentCropYear = $allCropYears->last() ?? '20232024';
    }
    
    // Build base query with optional week filter
    $summaryQuery = Summary::where('crop_year', $currentCropYear);
    
    if ($filterWeekNo) {
        $summaryQuery->where('week_no', $filterWeekNo);
    }
    
    // Calculate totals based on filters - only for the selected crop year
    $totalNetCane = (clone $summaryQuery)->sum(DB::raw('CAST(net_cane AS DECIMAL(12,3))'));
    $totalNetAmount = (clone $summaryQuery)->sum(DB::raw('CAST(net_amount AS DECIMAL(12,2))'));
    
    // Count planters based on filters - ONLY for selected crop year and week!
    $activePlantersCount = (clone $summaryQuery)->distinct('planter_code')->count('planter_code');
    
    // Total planters should also be filtered by crop year and week!
    $totalPlantersQuery = Summary::query();
    if ($filterCropYear) {
        $totalPlantersQuery->where('crop_year', $filterCropYear);
    }
    if ($filterWeekNo) {
        $totalPlantersQuery->where('week_no', $filterWeekNo);
    }
    $totalPlanters = $totalPlantersQuery->distinct('planter_code')->count('planter_code');
    
    // Previous year comparison
    $previousCropYear = Summary::where('crop_year', '<', $currentCropYear)
        ->orderBy('crop_year', 'desc')
        ->value('crop_year');
    
    $previousYearQuery = Summary::where('crop_year', $previousCropYear);
    if ($filterWeekNo) {
        $previousYearQuery->where('week_no', $filterWeekNo);
    }
    
    $lastYearData = $previousCropYear ? (clone $previousYearQuery)->sum(DB::raw('CAST(net_cane AS DECIMAL(12,3))')) : 0;
    
    $caneChange = 0;
    if ($lastYearData > 0) {
        $caneChange = (($totalNetCane - $lastYearData) / $lastYearData) * 100;
    }
    
    // Get current week for THE SELECTED crop year
    $maxWeek = Summary::where('crop_year', $currentCropYear)->max('week_no') ?? 0;
    
    // Get prices - filter by crop year and week
    $quedanPriceQuery = QuedanPrice::orderBy('created_at', 'desc');
    $molassesPriceQuery = MolassesPrice::orderBy('created_at', 'desc');
    
    if ($filterCropYear) {
        $quedanPriceQuery->where('crop_year', $filterCropYear);
        $molassesPriceQuery->where('crop_year', $filterCropYear);
    }
    if ($filterWeekNo) {
        $quedanPriceQuery->where('week_no', $filterWeekNo);
        $molassesPriceQuery->where('week_no', $filterWeekNo);
    }
    
    $latestQuedan = $quedanPriceQuery->first();
    $latestMolasses = $molassesPriceQuery->first();
    
    $quedanPrice = $latestQuedan ? (float) $latestQuedan->quedan_price : 0;
    $quedanType = $latestQuedan ? $latestQuedan->quedan_type : 'N/A';
    $quedanTime = $latestQuedan ? $latestQuedan->created_at->diffForHumans() : 'No data';
    
    $molassesPrice = $latestMolasses ? (float) $latestMolasses->mol_price : 0;
    $molassesTime = $latestMolasses ? $latestMolasses->created_at->diffForHumans() : 'No data';
    
    // Yearly production data
    $yearlyLabels = [];
    $yearlyData = [];
    
    foreach ($allCropYears as $cropYear) {
        $yearlyLabels[] = $cropYear;
        $yearSummaryQuery = Summary::where('crop_year', $cropYear);
        if ($filterWeekNo) {
            $yearSummaryQuery->where('week_no', $filterWeekNo);
        }
        $total = $yearSummaryQuery->sum(DB::raw('CAST(net_cane AS DECIMAL(12,3))'));
        $yearlyData[] = (float) $total;
    }
    
    // Weekly data for selected crop year
    $weeklyData = $this->getWeeklyData($currentCropYear);
    
    // Top planters based on filters
    $topPlanters = $this->getTopPlanters($currentCropYear, $filterWeekNo);
    
    // Recent prices
    $recentPrices = $this->getRecentPrices($filterCropYear, $filterWeekNo);
    
    // Activities
    $activities = $this->getActivities(
        $currentCropYear, 
        $filterWeekNo, 
        $quedanPrice, 
        $quedanType, 
        $molassesPrice, 
        $activePlantersCount, 
        $totalPlanters, 
        $maxWeek, 
        $quedanTime, 
        $molassesTime
    );
    
    return response()->json([
        'stats' => [
            'totalNetCane' => (float) $totalNetCane,
            'totalNetAmount' => (float) $totalNetAmount,
            'activePlanters' => $activePlantersCount,
            'totalPlanters' => $totalPlanters, // Now filtered!
            'currentCropYear' => $currentCropYear,
            'currentWeek' => (int) $maxWeek,
            'caneChange' => round($caneChange, 1),
            'amountChange' => 12.5,
            'quedanPrice' => $quedanPrice,
            'quedanType' => $quedanType,
            'molassesPrice' => $molassesPrice,
            'activeLoans' => 0,
            'totalLoanAmount' => 0,
            'totalMolasses' => 0
        ],
        'yearlyData' => [
            'labels' => $yearlyLabels,
            'datasets' => [['data' => $yearlyData]]
        ],
        'weeklyData' => $weeklyData,
        'activities' => $activities,
        'topPlanters' => $topPlanters,
        'recentPrices' => $recentPrices,
        'availableYears' => $allCropYears->values()
    ]);
}

private function getTopPlanters($cropYear, $weekNo = null)
{
    $query = Summary::where('crop_year', $cropYear);
    
    if ($weekNo) {
        $query->where('week_no', $weekNo);
    }
    
    $planters = $query
        ->selectRaw('planter_code, planter_name, SUM(CAST(net_cane AS DECIMAL(12,3))) as total_cane, SUM(CAST(net_amount AS DECIMAL(12,2))) as total_amount')
        ->groupBy('planter_code', 'planter_name')
        ->orderByDesc('total_cane') // Order by cane instead of amount for better representation
        ->limit(5)
        ->get();
    
    // Don't fall back to all data if empty - return empty collection
    // This ensures 20252026 shows blank when there's no data
    
    return $planters;
}

private function getWeeklyData($cropYear)
{
    $weeklySummaries = Summary::where('crop_year', $cropYear)
        ->selectRaw('week_no, SUM(CAST(net_cane AS DECIMAL(12,3))) as total_cane')
        ->groupBy('week_no')
        ->orderBy('week_no')
        ->limit(52)
        ->get();
    
    $labels = [];
    $data = [];
    
    foreach ($weeklySummaries as $item) {
        $labels[] = 'Week ' . $item->week_no;
        $data[] = (float) $item->total_cane;
    }
    
    // If no data for this crop year, return empty arrays
    // Remove the fallback data that was here before
    
    return [
        'labels' => $labels,
        'datasets' => [['data' => $data]]
    ];
}

private function getActivities($cropYear, $weekNo, $quedanPrice, $quedanType, $molassesPrice, $activePlanters, $totalPlanters, $maxWeek, $quedanTime, $molassesTime)
{
    $weekLabel = $weekNo ? "Week {$weekNo}" : ($maxWeek > 0 ? "Week {$maxWeek}" : 'N/A');
    
    // Check if there's actual production data for this crop year
    $hasProductionData = $totalPlanters > 0;
    
    return [
        [
            'id' => 1,
            'description' => $quedanPrice > 0 
                ? 'Quedan price: ₱' . number_format($quedanPrice, 2) . ' (Type: ' . $quedanType . ')'
                : 'No quedan price set for ' . $cropYear,
            'time' => $quedanTime,
            'icon' => 'fas fa-tag',
            'bgColor' => 'bg-green-100',
            'iconColor' => 'text-green-600'
        ],
        [
            'id' => 2,
            'description' => $hasProductionData 
                ? 'Current: Crop Year ' . $cropYear . ' - ' . $weekLabel
                : 'No production data for Crop Year ' . $cropYear,
            'time' => $hasProductionData ? 'Current season' : 'No data',
            'icon' => 'fas fa-calendar-week',
            'bgColor' => $hasProductionData ? 'bg-blue-100' : 'bg-gray-100',
            'iconColor' => $hasProductionData ? 'text-blue-600' : 'text-gray-400'
        ],
        [
            'id' => 3,
            'description' => $hasProductionData 
                ? $activePlanters . ' active planters in ' . $cropYear . ' (' . $totalPlanters . ' total)'
                : 'No planters in ' . $cropYear,
            'time' => $hasProductionData ? 'Current season' : 'No data',
            'icon' => 'fas fa-users',
            'bgColor' => $hasProductionData ? 'bg-purple-100' : 'bg-gray-100',
            'iconColor' => $hasProductionData ? 'text-purple-600' : 'text-gray-400'
        ],
        [
            'id' => 4,
            'description' => $molassesPrice > 0 
                ? 'Molasses price: ₱' . number_format($molassesPrice, 2)
                : 'No molasses price set for ' . $cropYear,
            'time' => $molassesTime,
            'icon' => 'fas fa-flask',
            'bgColor' => 'bg-orange-100',
            'iconColor' => 'text-orange-600'
        ]
    ];
}

private function getRecentPrices($cropYear = null, $weekNo = null)
{
    $quedanQuery = QuedanPrice::orderBy('created_at', 'desc');
    $molassesQuery = MolassesPrice::orderBy('created_at', 'desc');
    
    if ($cropYear) {
        $quedanQuery->where('crop_year', $cropYear);
        $molassesQuery->where('crop_year', $cropYear);
    }
    if ($weekNo) {
        $quedanQuery->where('week_no', $weekNo);
        $molassesQuery->where('week_no', $weekNo);
    }
    
    $quedanPrices = $quedanQuery->limit(5)->get();
    $molassesPrices = $molassesQuery->limit(5)->get();
    
    $prices = [];
    
    foreach ($quedanPrices as $item) {
        $prices[] = [
            'id' => 'q_' . $item->id,
            'date' => $item->created_at->format('M d, Y'),
            'type' => 'Quedan',
            'crop_year' => $item->crop_year,
            'week_no' => $item->week_no,
            'price' => (float) $item->quedan_price
        ];
    }
    
    foreach ($molassesPrices as $item) {
        $prices[] = [
            'id' => 'm_' . $item->id,
            'date' => $item->created_at->format('M d, Y'),
            'type' => 'Molasses',
            'crop_year' => $item->crop_year,
            'week_no' => $item->week_no,
            'price' => (float) $item->mol_price
        ];
    }
    
    usort($prices, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return array_slice($prices, 0, 8);
}

    public function dashboardWeekly(Request $request)
    {
        $year = $request->get('year', '20242025');
        $weeklyData = $this->getWeeklyData($year);
        return response()->json($weeklyData);
    }
}
