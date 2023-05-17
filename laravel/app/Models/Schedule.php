<?php

namespace App\Models;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    protected $fillable = [
        'store_id',
        'consumer_id',
        'event_id',
        'start_date',
        'end_date',
        'title',
        'description',
        'interview_venue',
        'type',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeWhereInPeriod(Builder $query, $start, $end): void
    {
        $query->where(function ($query) use ($start, $end) {
            $query->where(function ($query) use ($start, $end) {
                $query->where("start_date", ">=", $start)
                ->where("end_date", "<=", $end);
            })
            ->orWhere(function ($query) use ($start, $end) {
                $query->where("start_date", "<=", $start)
                ->where("end_date", ">=", $end);
            })
            ->orWhere(function ($query) use ($start, $end) {
                $query->where("start_date", ">", $start)
                ->where("start_date", "<", $end)
                ->where("end_date", ">", $end);
            })
            ->orWhere(function ($query) use ($start, $end) {
                $query->where("start_date", "<", $start)
                ->where("end_date", ">", $start)
                ->where("end_date", "<", $end);
            });
        });
    }
}
