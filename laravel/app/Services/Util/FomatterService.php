<?php

namespace App\Services\Util;

use Exception;
use Aws\Lambda\LambdaClient;
use Aws\Lambda\Exception\LambdaException;

class FomatterService
{
    /**
     * 
     * @param string $tel
     * @return bool
     */
    public static function validateTel($tel)
    {
        // 余計な文字等を削除
        $pattern    = '/[━.*‐.*―.*－.*\-.*ー.*\-]/i';
        $tel        = preg_replace($pattern, '', $tel);
        $tel        = str_replace(' ', '', $tel);
        $tel        = str_replace('　', '', $tel);
        
        if (empty($tel)) {
            \Log::notice('validate tel false : empty');
            return false;
        }

        if (!preg_match( '/^0[7-9]0[0-9]{8}$/', $tel )) {
            \Log::notice('validate tel false : num digit');
            \Log::notice($tel);
            return false;
        }
        return true;
    }
}
