<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CropYear;
use App\Models\WeekNo;

class MenuController extends Controller
{
    public function dashboard()
    {
        $cropYears = CropYear::pluck('crop_year', 'id');
        $weekNos = WeekNo::pluck('week_no', 'id');

    return view('menu.dashboard', compact('cropYears', 'weekNos'));
    }
}
