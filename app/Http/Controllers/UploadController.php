<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Summary;

class UploadController extends Controller
{
    public function uploadCSV(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt|max:2048',
    ]);

    $file = $request->file('file');
    $csvData = file_get_contents($file);
    $lines = array_map('str_getcsv', explode("\n", $csvData));

    foreach ($lines as $index => $line) {
    if (count($line) < 6) continue;

    Summary::create([
        'crop_year' => (int) preg_replace('/[^0-9]/', '', trim($line[0])),
        'week_no' => (int) $line[1],
        'planter_code' => (int) $line[2],
        'planter_name' => trim($line[3]),
        'net_cane' => (float) str_replace(',', '', $line[4]),
        'net_amount' => (float) str_replace(',', '', $line[5]),
    ]);
}

    return redirect()->back()->with('success', 'CSV imported successfully.');
}
}
