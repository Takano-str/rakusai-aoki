<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleSpreadSheet extends Model
{
    // use HasFactory;
    public static function instance() {
        $client = new \Google_Client();
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        // $googleDataArray = \Config::get('googleAccount.account');
        // $client->setAuthConfig($googleDataArray);
        $credentials_path = storage_path('app/json/service-account.json');
        $client->setAuthConfig($credentials_path);
        return new \Google_Service_Sheets($client);
    }
}
