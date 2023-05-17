<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Credentials\Credentials;
use Aws\Result;
use Aws\Sdk;
use Illuminate\Support\Facades\Log;

class ShortUrl
{
    // use HasFactory;
    /**
     * @var string 短縮URLの共通部分
     */
    public const BASE_SHORTENED_URL = 'https://nxc.3gk.me/';

    /**
     * @var string DynamoDBのテーブル名
     * アッパーキャメルケースで命名する
     */
    public const DYNAMO_DB_TABLE_NAME = 'RakusaiShortUrl';

    /**
     * DynamoDBクライアント
     *
     * @var DynamoDbClient
     */
    public static $dynamoDb;


    /**
     * 短縮URL生成
     *
     * @link https://bit.ly/30tydyu
     * @param string $url
     * @return string $resultUrl
     */
    public static function shorten(
        string $url
    ): string {
        $resultUrl = '';
        // DynamoDBで見つかったらそのURLを返す
        $savedUrl = self::getUrlOnDynamoDb($url);
        if (!empty($savedUrl)) {
            $resultUrl = $savedUrl;
            return $resultUrl;
        }
        $urlHash = self::getHash($url, 8);
        $shortenedUrl = sprintf('%s%s', self::BASE_SHORTENED_URL, $urlHash);
        if (self::setUrlOnDynamoDb($url, $shortenedUrl)) {
            $resultUrl = $shortenedUrl;
        }
        return $resultUrl;
    }

    /**
     * ハッシュ値を取得する
     *
     * @param string $string
     * @param integer $length
     * @return string
     */
    public static function getHash(string $string, int $length = 6): string
    {
        $character = array_merge(
            range('0', '9'),
            range('a', 'z'),
            range('A', 'Z')
        );
        $hash = hash('sha256', $string);  // ハッシュ値の取得
        $number = hexdec($hash);         // 16進数ハッシュ値を10進数
        $result = self::decNth($number, $character);      // 62進数に変換

        return substr($result, 0, $length); //$len の長さ文抜き出し
    }

    /**
     * 62進数変換
     *
     * @param int|float $number
     * @param array $character 文字配列
     * @return string $result 結果
     */
    public static function decNth($number, array $character): string
    {
        $base   = count($character);
        $result = '';

        while ($number > 0) {
            $result = $character[fmod($number, $base)] . $result;
            $number = floor($number / $base);
        }

        return empty($result) ? '' : $result;
    }

    /**
     * AWS DynamoDBに接続する
     *
     * @return DynamoDbClient $dynamoDb
     */
    public static function getDynamoDbClient(): DynamoDbClient
    {
        // if (is_a(self::$dynamoDb, 'Aws\DynamoDb\DynamoDbClient')) {
        //     Log::notice('DynamoDB クライアントインスタンス再利用');
        //     return self::$dynamoDb;
        // }
        $key = config('app.aws_access_key_id');
        $secret = config('app.aws_secret_access_key');
        $credentials = new Credentials($key, $secret);
        $sdk = new Sdk([
            'endpoint' => 'http://dynamodb.ap-northeast-1.amazonaws.com',
            'region'   => 'ap-northeast-1',
            'version'  => 'latest',
            'credentials' => $credentials
        ]);
        $dynamoDb = $sdk->createDynamoDb();
        // self::$dynamoDb = $dynamoDb;
        return $dynamoDb;
    }

    /**
     * AWS DynamoDBにURL設定
     *
     * @link https://qiita.com/Imyslx/items/f250cf2d24ac4f21a7e0
     * @access public
     * @param string $shortenedUrl 短縮URL
     * @param string $originalUrl 短縮前URL
     * @return bool $result 結果
     */
    public static function setUrlOnDynamoDb(string $originalUrl, string $shortenedUrl): bool
    {
        $dynamoDb = self::getDynamoDbClient();
        $item = [
            'OriginalUrl' => ['S' => $originalUrl],
            'ShortenedUrl' => ['S' => $shortenedUrl],
        ];
        $params = [
            'TableName' => self::DYNAMO_DB_TABLE_NAME,
            'Item' => $item,
        ];

        try {
            $result = $dynamoDb->putItem($params);
            Log::notice(['追加完了', $item]);
        } catch (DynamoDbException $e) {
            Log::critical(
                ['短縮URL追加失敗', $e->getAwsErrorMessage()],
                ['file' => __FILE__, 'line' => __LINE__]
            );
        }
        if (is_a($result, 'Aws\Result')) {
            return true;
        };
        return false;
    }

    /**
     * AWS DynamoDBから短縮前URL取得
     *
     * @link https://qiita.com/snoguchi/items/a16dfb831d6ef53d5f4e
     * @access public
     * @param string $originalUrl 短縮前URL
     * @return Result $result 結果
     */
    public static function getUrlOnDynamoDb(string $originalUrl): string
    {
        $url = '';
        $dynamoDb = self::getDynamoDbClient();
        $params = [
            'TableName' => self::DYNAMO_DB_TABLE_NAME,
            'Key' => [
                'OriginalUrl' => ['S' => $originalUrl],
            ],
        ];
        Log::notice(['検索条件', $params], ['file' => __FILE__, 'line' => __LINE__]);
        try {
            $result = $dynamoDb->getItem($params);
            $resultUrl = $result->search('Item.ShortenedUrl.S');
            if (!empty($resultUrl)) {
                $url = $resultUrl;
            }
            Log::notice($result->search('Item.ShortenedUrl'), ['file' => __FILE__, 'line' => __LINE__]);
        } catch (DynamoDbException $e) {
            Log::critical(['短縮URL取得失敗', $e->getAwsErrorMessage()], ['file' => __FILE__, 'line' => __LINE__]);
        }
        return $url;
    }
}
