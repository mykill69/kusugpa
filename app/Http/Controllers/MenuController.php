<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CropYear;
use App\Models\WeekNo;
use App\Models\Summary;

class MenuController extends Controller
{
    public function dashboard()
    {
        $cropYears = CropYear::pluck('crop_year', 'id');
        $weekNos = WeekNo::pluck('week_no', 'id');

    return view('menu.dashboard', compact('cropYears', 'weekNos'));
    }

    public function summaryReport()
{
    $summaries = Summary::orderBy('id', 'desc')->get();
    $cropYears = CropYear::pluck('crop_year', 'id');
    $weekNos = WeekNo::orderBy('week_no')->pluck('week_no');

    return view('menu.summaryReport', compact('summaries', 'cropYears', 'weekNos'));
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

    $summaries = $query->orderBy('week_no')->get();

    $pdf = Pdf::loadView('modal.viewSummaryPdf', compact('summaries', 'cropYear', 'weekFrom', 'weekTo'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('summary-report.pdf'); // ✅ This is correct
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

    $summaries = $query->orderBy('week_no')->get();

    $pdf = Pdf::loadView('modal.viewSummaryPdf', compact('summaries', 'cropYear', 'weekFrom', 'weekTo'))
                ->setPaper('a4', 'portrait');

    return $pdf->download('summary-report.pdf');
}


}
