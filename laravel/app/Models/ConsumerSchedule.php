<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerSchedule extends Model
{
    // use HasFactory;
    protected $fillable = [
        'consumer_id',
        'schedule_id',
    ];

    public function schedule()
    {
        return $this->hasOne(Schedule::class, 'id', 'schedule_id');
    }
}
