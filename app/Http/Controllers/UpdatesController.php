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
        'crop_year' => 'required|string|unique:crop_year,crop_year',
        'user_id' => 'required|exists:users,id',
    ]);

    \App\Models\CropYear::create($data);
    return response()->json(['message' => 'Crop Year added successfully']);
}

public function addWeekNumber(Request $request)
{
    $data = $request->validate([
        'crop_year' => 'required|string',
        'week_no' => 'required|string',
        'week_start_date' => 'required|date',
        'week_end_date' => 'required|date',
        'user_id' => 'required|exists:users,id',
    ]);

    \App\Models\WeekNo::create([
        'crop_year' => $data['crop_year'],
        'week_no' => $data['week_no'],
        'week_start_date' => $data['week_start_date'],
        'week_end_date' => $data['week_end_date'],
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

    \App\Models\QuedanPrice::create($data);

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

    \App\Models\MolassesPrice::create($data);

    return response()->json(['message' => 'Molasses Price added successfully']);
}

}
