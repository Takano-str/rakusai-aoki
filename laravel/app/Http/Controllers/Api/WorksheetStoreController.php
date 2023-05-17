<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consumer;
// use App\Models\Schedule;
// use App\Models\ScheduleHoliday;
use App\Models\Worksheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WorksheetStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        \Log::info($request);
        $consumerId = 1;

        try {
            $consumerId = Crypt::decrypt($request->input('csid', ''));
            if (empty($consumerId)) {
                throw new \Exception("consumer_id: {$consumerId} is not found.");
            }
        } catch (\Throwable $th) {
            \Log::notice($consumerId);
            \Log::error('decrypt失敗');
            /** @todo 404ページ **/
            return response()->json([], 404);
            // return view('auth.404');
        }

        $consumer = Consumer::with('worksheet')
            ->with('consumerDetail')
            ->where('id', $consumerId)
            ->first()
            ->toArray();
        $consumerDetail = $consumer['consumer_detail'] ?? [];
        $worksheet = $consumer['worksheet'] ?? [];

        $storeAnswer = $worksheet['store_answer'] ?? "";
        $storeId = $consumer['store_id'];

        $worksheetAnswer = $worksheet['worksheet_answer'] ?? "";
        $worksheetAnswerList = json_decode($worksheetAnswer, true);
        $adjustSchedules = $worksheetAnswerList['StepInterviewAnotherSchedule'] ?? [];
        $adjustScheduleCutList = $this->cutOffDate($adjustSchedules);

        \Log::debug($worksheetAnswerList);
        $storeAdjust = [
            'consumerId' => $consumerId,
            'name' => $consumerDetail['name'] ?? '',
            'ats_id' => $consumerDetail['ats_id'] ?? '',
            'tel' => $consumerDetail['tel'] ?? '',
            'mail' => $consumerDetail['mail'] ?? '',
            'adjustSchedules' => $adjustScheduleCutList,
            'worksheetAnswer' => $worksheetAnswerList
        ];
        return response()->json($storeAdjust, 200);
    }

    /**
     * 30分区切りのスケジュールを文字列の配列にして返す
     *
     * @param array $interviewDateAdjustList
     * @return array $cutOffDates
     */
    public function cutOffDate($interviewDateAdjustList)
    {
        $cutOffDates = [];
        $compareDay = new Carbon('now', 'Asia/Tokyo');
        $compareDay->addHour(36);

        try {
            foreach ($interviewDateAdjustList as $interviewDate) {
                $dateList = explode('_', $interviewDate);
                $timeList = explode('~', $dateList[1]);
                $startTimeList = explode(':', $timeList[0]);
                $endTimeList = explode(':', $timeList[1]);
                $diffTime = ($endTimeList[0] * 60 + $endTimeList[1]) - ($startTimeList[0] * 60 + $startTimeList[1]);
                $loopCount = $diffTime / 30;
                $nextStart = $timeList[0];

                for ($index = 1; $index <= $loopCount; $index++) {
                    $sumMinutes = ($startTimeList[0] * 60 + $startTimeList[1]) + ($index * 30);
                    $str30 = $sumMinutes % 60 == 0 ? '00' : '30';
                    $endTime = str_pad(strval(floor($sumMinutes / 60)), 2, '0', STR_PAD_LEFT) . ':' . $str30;
                    $appendTimeStr = $dateList[0] . ' ' . $nextStart . '~' . $dateList[0] . ' ' . $endTime;
                    $nextStart = $endTime;
                    $cutOffDates[] = $appendTimeStr;
                }
            }
        } catch (\Exception $e) {
            $cutOffDates = [];
            Log::error($e);
        }
        return $cutOffDates;
    }
}
