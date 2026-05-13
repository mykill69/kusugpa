<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropYear extends Model
{
    use HasFactory;

    protected $table = 'crop_year';

    protected $fillable = [
        'crop_year',
        'user_id',
    ];

    public function weeks()
    {
        return $this->hasMany(WeekNo::class, 'crop_year', 'crop_year');
    }
}