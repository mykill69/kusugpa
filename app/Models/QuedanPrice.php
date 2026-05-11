<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuedanPrice extends Model
{
    use HasFactory;

     protected $table = 'quedan_price';

      protected $fillable = [
        'quedan_type',
        'quedan_price',
        'crop_year',
        'week_no',
        'user_id',
    ];
}
