<?php

namespace App\Services\Google;

use App\Models\GoogleSpreadSheet;
use Google_Service_Sheets_ValueRange;
use Log;

class SpreadService
{
    /**
     * @var string
     */
    private $spreadId;
    private $range;
    private $sheets;

    public function __construct($spreadId, $range)
    {
        $this->spreadId = $spreadId;
        $this->range = $range;
        $this->sheets = GoogleSpreadSheet::instance();
        // $response = $this->sheets->spreadsheets_values->get($sheet_id, $range);
        // $values = $response->getValues();
    }

    // スプレッドの指定した範囲の値を取得
    public function getSpreadRangeValues($range)
    {
        $response = $this->sheets->spreadsheets_values->get($this->spreadId, $range);
        $values = $response->getValues();
        return $values;
    }

    // スプレッドの全範囲の値を取得
    public function getSpreadValues()
    {
        $response = $this->sheets->spreadsheets_values->get($this->spreadId, $this->range);
        $values = $response->getValues();
        return $values;
    }

    // 行を挿入
    // インサートしたい一行を配列で渡す
    public function insertSpread($values)
    {
        $body = new Google_Service_Sheets_ValueRange(
            [
                'values' => [$values],
            ]
        );
        $response = $this->sheets->spreadsheets_values->append(
            $this->spreadId,
            $this->range,
            $body,
            [
                'valueInputOption' => 'USER_ENTERED',
                'insertDataOption' => 'INSERT_ROWS',
            ]
        );
    }

    // セル更新
    public function updateSpreadCell($updateValues)
    {
        $body = new Google_Service_Sheets_ValueRange(
            [
                'values' => $updateValues,
            ]
        );
        $body->setValues($updateValues);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $result = $this->sheets->spreadsheets_values->update(
            $this->spreadId,
            $this->range,
            $body,
            $params
        );
        return $result->getUpdatedCells();
    }

    // 1セルのみ更新
    public function updateSpreadOneCell($cell, $updateValue)
    {
        $body = new Google_Service_Sheets_ValueRange(
            [
                'values' => [$updateValue],
            ]
        );
        $body->setValues($updateValue);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $result = $this->sheets->spreadsheets_values->update(
            $this->spreadId,
            $cell,
            $body,
            $params
        );
        return $result->getUpdatedCells();
    }

    // 1列から1セル検索
    /**
     * @return int
     */
    public function searchOneCell($targetValue, $col): int
    {
        $response = $this->sheets->spreadsheets_values->get($this->spreadId, $col);
        $values = $response->getValues();
        $rowNum = 0;
        foreach ($values as $index => $value) {
            if (!empty($value)) {
                if ($value[0] == $targetValue) {
                    Log::debug($values);
                    $rowNum += 1;
                    return $rowNum;
                }
            }
            $rowNum += 1;
        }
        return $rowNum;
    }

    // 1セルのみ更新
    public function updateSpreadRow($cell, $values)
    {
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values,
        ]);
        // $body->setValues($values);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $result = $this->sheets->spreadsheets_values->append(
            $this->spreadId,
            $cell,
            $body,
            $params
        );
        // return $result->getUpdatedCells();
        return true;
    }
}
