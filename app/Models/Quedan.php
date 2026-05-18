<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quedan extends Model
{
    use HasFactory;

    protected $table = 'quedans';

    protected $fillable = [
        'crop_year', 'week_no', 'planter_code', 'planter_name',
        'qdn_no', 'tin_no', 'total_liens', 'sugar_lkg', 'labor_lkg', 'user_id','status', 
        'bought_at', 'bought_by',

    ];
}