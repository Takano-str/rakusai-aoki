<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worksheet extends Model
{
    // use HasFactory;
    protected $fillable = [
        'consumer_id',
        'store_id',
        'worksheet_url', 
        'worksheet_answer', 
        'store_url', 
        'store_answer', 
    ];
}
