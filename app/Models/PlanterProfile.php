<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanterProfile extends Model
{
    use HasFactory;

    protected $table = 'planter_profiles';

    protected $fillable = [
        'planter_code',
        'planter_name',
        'contact_number',
        'address',
        'area_location',
        'total_area',
        'status',
        'membership_date',
        'notes',
        'crop_year',
    ];

    protected $casts = [
        'membership_date' => 'date',
    ];
}