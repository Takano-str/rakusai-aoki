<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //use HasFactory;
    protected $fillable = [
        'consumer_id',
        // 'company_id',
        // 'send_status',
        'message',
        'confirm_time',
    ];
}
