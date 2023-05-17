<?php

namespace App\Services\Util;

use App\Models\ShortUrl;
use Exception;

class CreateUrlService
{
    /**
     * @param  String $path
     * @param  Array $params
     * @return String
     */
    public static function makeWorksheetUrl($path, $params)
    {
        $urlParam = '';
        foreach($params as $key => $val) {
            $urlParam .= $urlParam == '' ? '?' : '&';
            $urlParam .= $key . '=' . $val;
        }
        // $createUrl = url("/" . $path . $urlParam);
        $createUrl = config('app.frontend_url') . "/" . $path . $urlParam;
        try {
            $createUrl = ShortUrl::shorten($createUrl);
        } catch (\Throwable $th) {
            //throw $th;
            \Log::error($th);
        }
        return $createUrl;
    }
}
