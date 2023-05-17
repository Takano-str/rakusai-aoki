<?php

namespace App\Http\Controllers\Api;

use App\Models\Consumer;
use App\Models\ConsumerSchedule;
use App\Models\Message;
use App\Models\Schedule;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $consumer_id = '';
        $consumer_id = Crypt::decrypt($request->input('csid', ''));

        $consumer = Consumer::with('store')
            ->with('consumerDetail')
            ->with('worksheet')
            ->where('id', $consumer_id)
            ->first()
            ->toArray();

        $consumerId = $consumer['id'];
        $storeId = $consumer['store_id'];

        $consumerSchedule = ConsumerSchedule::where('consumer_id', $consumer_id)
            ->first()
            ->toArray();
            // \Log::debug($consumerSchedule);
        $scheduleId = $consumerSchedule['schedule_id'];

        $decideSchedule = Schedule::select([
            'start_date',
            'end_date',
            'interview_venue',
            // 'consumer_id',
            'store_id',
            // 'type',
        ])->where('id', $scheduleId)
            // ->where('store_id', $storeId)
            // ->where('type', 1)
            ->first() ?? [];
        if (!empty($decideSchedule)) {
            $decideSchedule = $decideSchedule->toArray();
        }

        // \Log::debug($consumer);

        Message::where('consumer_id', $consumer_id)
            ->update([
                'confirm_time' => Carbon::now('Asia/Tokyo')->format('Y-m-d H:i:s')
            ]);

        $startDate = $decideSchedule['start_date'] ?? '';
        $endDate = $decideSchedule['end_date'] ?? '';
        $interviewVenue = $decideSchedule['interview_venue'] ?? '';
        $storeId = $decideSchedule['store_id'] ?? '';

        // $startDate = \str_replace('-', '/', $startDate);
        // $startDate = \str_replace(':30:00', ':30', $startDate);
        // $startDate = \str_replace(':00:00', ':00', $startDate);
        // $endDate = \str_replace('-', '/', $endDate);
        // $endDate = \str_replace(':30:00', ':30', $endDate);
        // $endDate = \str_replace(':00:00', ':00', $endDate);

        /** @todo 判定甘いので後で直す **/ 
        $googleMeetUrls = [];
        $venueAddress = '';
        if (strpos($interviewVenue, "web") !== false) {
            $googleMeetUrls = config('venueGoogleMeetUrlList')[$interviewVenue] ?? [];
        } else {
            /** 面接場所の住所の取得 @todo **/
            $venueAddressList = config('venueAddressList');
            foreach ($venueAddressList as $keyword => $address) {
                if (strpos($interviewVenue, $keyword) !== false) {
                    $venueAddress = $address;
                    break;
                }
            }
        }

        $startDate = (new Carbon($startDate))->isoFormat('YYYY/MM/DD(ddd) HH:mm');
        $endDate = (new Carbon($endDate))->isoFormat('YYYY/MM/DD(ddd) HH:mm');

        $worksheet = [
            'ats_id' => $consumer['ats_id'] ?? 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'interview_venue' => $interviewVenue,
            'store_id' => $storeId,
            'google_meet_urls' => $googleMeetUrls,
            'venue_address' => $venueAddress,
        ];

        return response()->json($worksheet, 200);
    }
}
