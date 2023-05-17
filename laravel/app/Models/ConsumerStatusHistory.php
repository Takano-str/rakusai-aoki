<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerStatusHistory extends Model
{
    // use HasFactory;
    protected $fillable = [
        'consumer_id',
        'history_number',
        'status_code',
        'changer_id',
    ];
}
