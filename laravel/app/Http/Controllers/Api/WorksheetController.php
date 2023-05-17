<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consumer;
use App\Models\Schedule;
use App\Models\ScheduleHoliday;
use App\Models\Worksheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WorksheetController extends Controller
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
        $isAnswered = false;

        try {
            $consumerId = $request->input('csid', '');
            $consumerKey = Crypt::decrypt($request->input('cskey', ''));
            if (empty($consumerId) || empty($consumerKey)) {
                throw new \Exception("consumer_id: {$consumerId} is not found.");
            }
            if ($consumerId != $consumerKey) {
                throw new \Exception("consumer_key is invalid.");
            }
        } catch (\Throwable $th) {
            \Log::notice($consumerId);
            \Log::notice($consumerKey);
            \Log::error('decrypt失敗');
            /** @todo 404ページ **/
            return response()->json([], 404);
            // return view('auth.404');
        }

        $consumer = Consumer::with('worksheet')
            ->where('id', $consumerId)
            ->first()
            ->toArray();
        \Log::debug($consumer);

        $storeId = $consumer['store_id'];
        $worksheetAnswer = $consumer['worksheet']['worksheet_answer'] ?? "";
        if (!empty($worksheetAnswer)) {
            $isAnswered = true;
        }

        $searchStartDateFrom    = (new Carbon())->addDays(1)->addHours(2)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s');
        $searchStartDateTo      = (new Carbon())->addDays(7)->setMinutes(0)->setSeconds(0)->format('Y-m-d H:i:s');

        // スケジュール取得
        $schedules = Schedule::select(
            'id',
            'start_date',
            'end_date',
            'store_id',
            'title',
            'interview_venue',
        )
            ->where('start_date', '>=', $searchStartDateFrom)
            ->where('start_date', '<=', $searchStartDateTo)
            ->where('store_id', $storeId)
            // ->where('type', config('schedule.type.empty.value'))
            ->whereIn('type', [
                config('schedule.type.empty.value'),
                config('schedule.type.jobop_available.value'),
            ])
            ->orderBy('start_date', 'asc')
            ->get()
            ->toArray();

        // \Log::notice($schedules);
        // \Log::notice(array_column($schedules, 'interview_venue'));
        // \Log::notice(array_unique(array_column($schedules, 'interview_venue')));
        $interviewVenues = array_unique(array_column($schedules, 'interview_venue')) ?? [];
        $interviewVenues = array_values($interviewVenues);
        // \Log::notice($interviewVenues);

        // 面接会場のキーでまとめる
        $schedulesForWorksheet = [];
        foreach ($interviewVenues as $interviewVenue) {
            $schedulesForWorksheet[$interviewVenue] = [];
        }
        foreach ($schedules as $schedule) {
            $venue = $schedule['interview_venue'];
            $schedulesForWorksheet[$venue][] = $schedule;
        }
        // \Log::debug($schedulesForWorksheet);

        $schedule_holidays = ScheduleHoliday::whereNotNull('event_id')
            ->get()
            ->toArray();
        $start = config('calendar.calc.start.day') + 1;
        $end   = config('calendar.calc.end.day');
        $today = Carbon::parse('today');
        foreach (range($start, $end) as $day) {
            $dates[] = $today->copy()->addDays($day);
        }
        foreach ($dates as $index => &$date) {
            // 土日を除く
            if ($date->isWeekend()) {
                unset($dates[$index]);
                continue;
            }
            // 祝日・指定休日を除く
            foreach ($schedule_holidays as $holiday) {
                $hs = Carbon::parse($holiday['start_date']);
                $he = Carbon::parse($holiday['end_date']);
                if ($date->lt($he) && $hs->lt($date->copy()->addDays())) {
                    unset($dates[$index]);
                    continue 2;
                }
            }
            $date = $date->format('Y/m/d/w');
        }
        $anotherDateArray = array_values($dates);

        // \Log::debug('休日の取得!');
        // \Log::debug($anotherDateArray);

        $startSelectTimeArray = [
            '10:00', '11:00',
            '12:00', '13:00', '14:00',
            '15:00', '16:00', '17:00',
            '18:00', '19:00'
        ];
        $endSelectTimeArray = [
            '11:00', '12:00',
            '13:00', '14:00', '15:00',
            '16:00', '17:00', '18:00',
            '19:00', '20:00'
        ];
        $weekString = [
            '日', //0
            '月', //1
            '火', //2
            '水', //3
            '木', //4
            '金', //5
            '土', //6
        ];

        $worksheet = [
            'consumerId'           => $consumerId,
            'isAnswered'           => $isAnswered,
            'schedules'            => $schedules,
            'interviewVenues'       => $interviewVenues,
            'schedulesForWorksheet' => $schedulesForWorksheet,
            'anotherDateArray'     => $anotherDateArray,
            'startSelectTimeArray' => $startSelectTimeArray,
            'endSelectTimeArray'   => $endSelectTimeArray,
            'weekString'           => $weekString,
            'blackListFlag'        => $consumer['black_list_flag'] ?? 0,
        ];

        // \Log::debug($worksheet);

        return response()->json($worksheet, 200);
    }
}
