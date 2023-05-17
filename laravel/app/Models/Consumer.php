<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    // use HasFactory;
    protected $fillable = [
        'consumer_id',
        'name', 
        'ats_id', 
        'company_id',
        'store_id',
        'admin_status',
        'past_apply_flag',
    ];

    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }
    public function consumerDetail()
    {
        return $this->hasOne(ConsumerDetail::class, 'consumer_id');
    }
    public function worksheet()
    {
        return $this->hasOne(Worksheet::class, 'consumer_id');
    }
}
