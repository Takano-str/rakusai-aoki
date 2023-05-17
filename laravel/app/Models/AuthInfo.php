<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthInfo extends Model
{
    protected $fillable = [
        'access_token', 
        'expires_in', 
        'scope',
        'token_type',
        'created',
        'refresh_token',
    ];
}
