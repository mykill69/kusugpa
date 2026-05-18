<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuedanPrice;
use App\Models\MolassesPrice;
use App\Models\CropYear;
use App\Models\WeekNo;
use App\Models\Quedan;
use App\Models\Molass;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PriceController extends Controller
{
    /**
     * Display the price management page
     */
    public function index()
    {
        $quedanPrices = QuedanPrice::orderBy('created_at', 'desc')->get();
        $molassesPrices = MolassesPrice::orderBy('created_at', 'desc')->get();
        $cropYears = CropYear::orderBy('crop_year', 'desc')->get();
        $weekNos = WeekNo::select('week_no')->distinct()->orderBy('week_no')->get();

        $cropYearsData = $cropYears->map(function($c) {
            return [
                'id' => $c->id,
                'crop_year' => $c->crop_year,
            ];
        });

        $quedanPricesData = $quedanPrices->map(function($q) {
            return [
                'id' => $q->id,
                'crop_year' => $q->crop_year,
                'week_no' => $q->week_no,
                'quedan_type' => $q->quedan_type,
                'quedan_price' => (float) $q->quedan_price,
                'created_at' => $q->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $molassesPricesData = $molassesPrices->map(function($m) {
            return [
                'id' => $m->id,
                'crop_year' => $m->crop_year,
                'week_no' => $m->week_no,
                'mol_price' => (float) $m->mol_price,
                'created_at' => $m->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $weekNosData = $weekNos->pluck('week_no');

        return view('prices.index', compact(
            'cropYearsData', 
            'quedanPricesData', 
            'molassesPricesData', 
            'weekNosData'
        ));
    }

    /**
     * Update quedan price
     */
    public function updateQuedanPrice(Request $request, QuedanPrice $quedanPrice)
    {
        $request->validate([
            'quedan_type' => 'required|string|max:255',
            'quedan_price' => 'required|numeric|min:0',
            'crop_year' => 'required|string',
            'week_no' => 'required|string',
        ]);

        $quedanPrice->update($request->only(['quedan_type', 'quedan_price', 'crop_year', 'week_no']));
        
        Log::info('Quedan Price updated: ' . $quedanPrice->id . ' by user: ' . auth()->user()->username);
        
        return response()->json(['message' => 'Quedan Price updated successfully']);
    }

    /**
     * Delete quedan price
     */
    public function destroyQuedanPrice(QuedanPrice $quedanPrice)
    {
        $quedanPrice->delete();
        Log::info('Quedan Price deleted: ' . $quedanPrice->id . ' by user: ' . auth()->user()->username);
        return response()->json(['message' => 'Quedan Price deleted successfully']);
    }

    /**
     * Update molasses price
     */
    public function updateMolassesPrice(Request $request, MolassesPrice $molassesPrice)
    {
        $request->validate([
            'mol_price' => 'required|numeric|min:0',
            'crop_year' => 'required|string',
            'week_no' => 'required|string',
        ]);

        $molassesPrice->update($request->only(['mol_price', 'crop_year', 'week_no']));
        
        Log::info('Molasses Price updated: ' . $molassesPrice->id . ' by user: ' . auth()->user()->username);
        
        return response()->json(['message' => 'Molasses Price updated successfully']);
    }

    /**
     * Delete molasses price
     */
    public function destroyMolassesPrice(MolassesPrice $molassesPrice)
    {
        $molassesPrice->delete();
        Log::info('Molasses Price deleted: ' . $molassesPrice->id . ' by user: ' . auth()->user()->username);
        return response()->json(['message' => 'Molasses Price deleted successfully']);
    }

    public function registry()
{
    $quedans = Quedan::orderBy('planter_name')->get();
    $molassesList = Molass::orderBy('planter_name')->get();
    $cropYears = CropYear::orderBy('crop_year')->pluck('crop_year');
    
    return view('prices.registry', compact('quedans', 'molassesList', 'cropYears'));
}

public function registryData(Request $request)
{
    $type = $request->get('type', 'quedan');
    $cropYear = $request->get('crop_year');
    $weekNo = $request->get('week_no');
    
    if ($type === 'quedan') {
        $query = Quedan::orderBy('planter_name');
    } else {
        $query = Molass::orderBy('planter_name');
    }
    
    if ($cropYear) {
        $query->where('crop_year', $cropYear);
    }
    if ($weekNo) {
        $query->where('week_no', $weekNo);
    }
    
    $data = $query->get();
    $weeks = [];
    
    if ($cropYear) {
        if ($type === 'quedan') {
            $weeks = Quedan::where('crop_year', $cropYear)->select('week_no')->distinct()->orderBy('week_no')->pluck('week_no');
        } else {
            $weeks = Molass::where('crop_year', $cropYear)->select('week_no')->distinct()->orderBy('week_no')->pluck('week_no');
        }
    }
    
    $stats = [
        'total' => $data->count(),
        'bought' => $data->where('status', 'bought')->count(),
        'pending' => $data->where('status', 'pending')->count(),
    ];
    
    return response()->json([
        'data' => $data,
        'weeks' => $weeks,
        'stats' => $stats,
    ]);
}

public function bulkUpdateQuedan(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'status' => 'required|in:bought,rejected,pending',
    ]);

    Quedan::whereIn('id', $request->ids)->update([
        'status' => $request->status,
        'bought_at' => $request->status === 'bought' ? now() : null,
        'bought_by' => auth()->id(),
    ]);

    AuditLog::log('update', 'quedan', 'Bulk updated ' . count($request->ids) . ' quedans to ' . $request->status);

    return response()->json(['message' => count($request->ids) . ' quedans updated successfully']);
}

public function bulkUpdateMolasses(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'status' => 'required|in:bought,rejected,pending',
    ]);

    Molass::whereIn('id', $request->ids)->update([
        'status' => $request->status,
        'bought_at' => $request->status === 'bought' ? now() : null,
        'bought_by' => auth()->id(),
    ]);

    AuditLog::log('update', 'molasses', 'Bulk updated ' . count($request->ids) . ' molasses to ' . $request->status);

    return response()->json(['message' => count($request->ids) . ' molasses updated successfully']);
}

public function buyQuedan()
{
    $cropYears = CropYear::orderBy('crop_year')->pluck('crop_year');
    return view('prices.buy-quedan', compact('cropYears'));
}

public function buyMolasses()
{
    $cropYears = CropYear::orderBy('crop_year')->pluck('crop_year');
    return view('prices.buy-molasses', compact('cropYears'));
}


public function exportQuedanPDF()
{
    $quedans = Quedan::orderBy('planter_name')->get();
    $pdf = Pdf::loadView('pdf.quedan-registry', [
        'quedans' => $quedans,
        'generatedDate' => now()->format('F d, Y H:i:s'),
    ])->setPaper('a4', 'landscape');
    return $pdf->download('quedan-registry-' . now()->format('Y-m-d') . '.pdf');
}

public function exportMolassesPDF()
{
    $molassesList = Molass::orderBy('planter_name')->get();
    $pdf = Pdf::loadView('pdf.molasses-registry', [
        'molassesList' => $molassesList,
        'generatedDate' => now()->format('F d, Y H:i:s'),
    ])->setPaper('a4', 'landscape');
    return $pdf->download('molasses-registry-' . now()->format('Y-m-d') . '.pdf');
}

}