<?php

namespace App\Services\Aws;

use Exception;
use Aws\Lambda\LambdaClient;
use Aws\Lambda\Exception\LambdaException;

class LambdaService
{
    /**
     * Lambdaリクエスト
     * @param string $lambdaFunctionName
     * @param array  $requestInfo
     */
    public static function apiRequest($lambdaFunctionName, $requestInfo)
    {
        if (config('app.lambda_env') != 'production') {
            $lambdaFunctionName = $lambdaFunctionName . '-test';
        }
        $result = 'success';
        try {
            $client = new LambdaClient([
                "region"      => "ap-northeast-1",
                'version'     => 'latest',
                'credentials' => [
                    'key'     => config('app.aws_access_key_id'),
                    'secret'  => config('app.aws_secret_access_key'),
                ],
            ]);
            $result = $client->invoke([
                'FunctionName'   => $lambdaFunctionName,
                'InvocationType' => 'Event',
                'Payload'        => json_encode($requestInfo),
            ]);
        // } catch (LambdaException $e) {
        //     report($e);
        } catch (Exception $e) {
            report($e);
            $result = 'failure';
        }
        return $result;
    }
}
