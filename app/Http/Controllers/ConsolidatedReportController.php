<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsolidatedUpload;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsolidatedReportController extends Controller
{
    public function index(Request $request)
{
    AuditLog::log('view', 'reports', 'Viewed consolidated report');
    
    // Load ALL data for client-side filtering (up to 2000)
    $uploads = ConsolidatedUpload::orderBy('planter_name')->limit(2000)->get();
    
    $totals = [
        'total_records' => ConsolidatedUpload::count(),
        'total_ta_amount' => ConsolidatedUpload::sum('ta_amount'),
        'total_emi_amount' => ConsolidatedUpload::sum('emi_amount'),
        'total_pat_amount' => ConsolidatedUpload::sum('pat_amount'),
        'total_fuel' => ConsolidatedUpload::sum('fuel_issuance_amt'),
        'total_rental' => ConsolidatedUpload::sum('rental_amt'),
        'total_underload' => ConsolidatedUpload::sum('underload_amt'),
        'total_mudpress' => ConsolidatedUpload::sum('mudpress_amt'),
        'total_adj' => ConsolidatedUpload::sum('adj_amt'),
        'total_summary' => ConsolidatedUpload::sum('total_summary'), // NEW
    ];
    
    return view('reports.consolidated', compact('uploads', 'totals'));
}
    
    public function exportPDF(Request $request)
    {
        $query = ConsolidatedUpload::orderBy('planter_name');
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('planter_name', 'like', "%{$search}%")
                  ->orWhere('planter_code', 'like', "%{$search}%");
            });
        }
        
        $uploads = $query->take(500)->get();
        
        AuditLog::log('export', 'reports', 'Exported consolidated report PDF', [
            'records' => $uploads->count()
        ]);
        
        $pdf = Pdf::loadView('pdf.consolidated-report', [
            'uploads' => $uploads,
            'generatedDate' => now()->format('F d, Y H:i:s'),
        ])->setPaper('a4', 'landscape');
        
        return $pdf->download('consolidated-report-' . now()->format('Y-m-d') . '.pdf');
    }
}