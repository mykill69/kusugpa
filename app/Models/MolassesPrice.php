<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MolassesPrice extends Model
{
    use HasFactory;

     protected $table = 'mol_price';

      protected $fillable = [
        'mol_price',
        'crop_year',
        'week_no',
        'user_id',
    ];
}
