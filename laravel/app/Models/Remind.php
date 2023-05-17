<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remind extends Model
{
    // use HasFactory;
    protected $fillable = [
        'consumer_id',
        'company_id',
        'message_type',
        'send_text',
        'send_time',
        'next_status',
    ];

    const NEXT_STATUS_CANCEL                 = 'cancel';
    const NEXT_STATUS_FAILURE                = 'failure';
    const NEXT_STATUS_PASS                   = 'pass';
    const NEXT_STATUS_BEFORE                 = 'the_day_before';
    const NEXT_STATUS_THE_TWO_DAYS_BEFORE1   = 'the_two_days_before';
    const NEXT_STATUS_THE_DAY_BEFORE1        = 'the_day_before_1';
    const NEXT_STATUS_THE_DAY_BEFORE2        = 'the_day_before_2';
    const NEXT_STATUS_FAILURE_BLACK_LIST     = 'failure_black_list';
    const NEXT_STATUS_FAILURE_BLACK_LIST_2   = 'failure_black_list_2';
    const NEXT_STATUS_SECOND                 = 'second';

    public function consumerDetail()
    {
        return $this->hasOne(ConsumerDetail::class, 'consumer_id', 'consumer_id');
    }
}
