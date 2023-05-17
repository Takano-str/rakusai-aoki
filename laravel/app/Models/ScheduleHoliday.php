<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleHoliday extends Model
{
    protected $fillable = [
        'event_id',
        'title',
        'start_date',
        'end_date',
    ];
}
