<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewInfoForSpreadsheet extends Model
{
    // use HasFactory;
    protected $fillable = [
        'consumer_id',
        'schedule_id', 
        'decide_date',
        'write_status',
        'option',
    ];
    public function consumer()
    {
        return $this->hasOne(Consumer::class, 'id', 'consumer_id');
    }
}
