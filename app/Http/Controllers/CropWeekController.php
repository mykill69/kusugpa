<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CropYear;
use App\Models\WeekNo;
use Illuminate\Support\Facades\Log;

class CropWeekController extends Controller
{
    /**
     * Display the crop year and week management page
     */
    public function index()
{
    $cropYears = CropYear::orderBy('crop_year', 'desc')->get();
    $weeks = WeekNo::with('cropYearRelation')->orderBy('crop_year', 'desc')->orderBy('week_no', 'asc')->get();
    
    // Prepare data for JavaScript
    $cropYearsData = $cropYears->map(function($c) {
        return [
            'id' => $c->id,
            'crop_year' => $c->crop_year,
            'weeks_count' => $c->weeks->count()
        ];
    });
    
    $weeksData = $weeks->map(function($w) {
        return [
            'id' => $w->id,
            'crop_year' => $w->crop_year,
            'week_no' => $w->week_no,
            'week_start_date' => $w->week_start_date ? $w->week_start_date->format('Y-m-d H:i') : null,
            'week_end_date' => $w->week_end_date ? $w->week_end_date->format('Y-m-d H:i') : null,
        ];
    });
    
    return view('crop-weeks.index', compact('cropYears', 'weeks', 'cropYearsData', 'weeksData'));
}

    /**
     * Update crop year
     */
    public function updateCropYear(Request $request, CropYear $cropYear)
    {
        $request->validate([
            'crop_year' => 'required|string|size:8|unique:crop_year,crop_year,' . $cropYear->id,
        ]);

        $cropYear->update(['crop_year' => $request->crop_year]);
        
        Log::info('Crop Year updated: ' . $cropYear->crop_year . ' by user: ' . auth()->user()->username);
        
        return response()->json(['message' => 'Crop Year updated successfully']);
    }

    /**
     * Delete crop year
     */
    public function destroyCropYear(CropYear $cropYear)
    {
        // Check if crop year has weeks
        $weekCount = WeekNo::where('crop_year', $cropYear->crop_year)->count();
        
        if ($weekCount > 0) {
            return response()->json([
                'message' => 'Cannot delete crop year with ' . $weekCount . ' associated week(s). Delete the weeks first.'
            ], 400);
        }
        
        $cropYear->delete();
        
        Log::info('Crop Year deleted: ' . $cropYear->crop_year . ' by user: ' . auth()->user()->username);
        
        return response()->json(['message' => 'Crop Year deleted successfully']);
    }

    /**
     * Update week number
     */
    public function updateWeek(Request $request, WeekNo $weekNo)
    {
        $request->validate([
            'crop_year' => 'required|string|size:8',
            'week_no' => 'required|integer|min:1|max:52',
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after:week_start_date',
        ]);

        $weekNo->update([
            'crop_year' => $request->crop_year,
            'week_no' => $request->week_no,
            'week_start_date' => $request->week_start_date,
            'week_end_date' => $request->week_end_date,
        ]);
        
        Log::info('Week updated: ' . $weekNo->id . ' by user: ' . auth()->user()->username);
        
        return response()->json(['message' => 'Week updated successfully']);
    }

    /**
     * Delete week number
     */
    public function destroyWeek(WeekNo $weekNo)
    {
        $weekNo->delete();
        
        Log::info('Week deleted: ' . $weekNo->id . ' by user: ' . auth()->user()->username);
        
        return response()->json(['message' => 'Week deleted successfully']);
    }
}
