<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CropYear;
use App\Models\WeekNo;
use App\Models\QuedanPrice;
use App\Models\MolassesPrice;

class UpdatesController extends Controller
{
    public function addCropYear(Request $request)
    {
        $data = $request->validate([
            'crop_year' => 'required|string|size:8|unique:crop_year,crop_year',
            'user_id' => 'required|exists:users,id',
        ]);

        CropYear::create($data);
        return response()->json(['message' => 'Crop Year added successfully']);
    }

    public function addWeekNumber(Request $request)
{
    $data = $request->validate([
        'crop_year' => 'required|string',
        'week_no' => 'required|string',
        'week_start_date' => 'required|date_format:Y-m-d H:i:s',
        'week_end_date' => 'required|date_format:Y-m-d H:i:s|after:week_start_date',
        'user_id' => 'required|exists:users,id',
    ]);

    // Convert datetime string to proper format
    $startDateTime = \Carbon\Carbon::parse($data['week_start_date']);
    $endDateTime = \Carbon\Carbon::parse($data['week_end_date']);

    WeekNo::create([
        'crop_year' => $data['crop_year'],
        'week_no' => $data['week_no'],
        'week_start_date' => $startDateTime,
        'week_end_date' => $endDateTime,
        'user_id' => $data['user_id'],
    ]);

    return response()->json(['message' => 'Week Number added successfully']);
}

    public function addQuedanPrice(Request $request)
    {
        $data = $request->validate([
            'quedan_type' => 'required|string',
            'quedan_price' => 'required|numeric',
            'crop_year' => 'required|string',
            'week_no' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        QuedanPrice::create($data);
        return response()->json(['message' => 'Quedan Price added successfully']);
    }

    public function addMolassesPrice(Request $request)
    {
        $data = $request->validate([
            'mol_price' => 'required|numeric',
            'crop_year' => 'required|string',
            'week_no' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        MolassesPrice::create($data);
        return response()->json(['message' => 'Molasses Price added successfully']);
    }
}