<?php

namespace App\Models;

use App\Model\Schedule;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{

    protected $fillable = [
        'unique_store_id',
        'company_id',
        'store_mail',
        'store_tel',
        'store_type',
        'store_name',
        'google_account',
        'whereby_url',
        'result_urls',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
