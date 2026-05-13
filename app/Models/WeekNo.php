<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekNo extends Model
{
    use HasFactory;

    protected $table = 'week_no';

    protected $fillable = [
        'crop_year',
        'week_no',
        'week_start_date',
        'week_end_date',
        'user_id',
    ];

  
    
    // Add accessors to format the date for the view
    public function getWeekStartDateAttribute($value)
    {
        return $value;
    }
    
    public function getWeekEndDateAttribute($value)
    {
        return $value;
    }
}