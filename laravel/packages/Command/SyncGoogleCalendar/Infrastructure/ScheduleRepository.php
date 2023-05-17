<?php

namespace Packages\Command\SyncGoogleCalendar\Infrastructure;

use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleRepository
{
    public function getNotEmptyByStoreID(string $storeID, Carbon $start, Carbon $end)
    {
        return Schedule::query()
            ->where("store_id", $storeID)
            ->whereNotIn("type", [
                config('schedule.type.empty.value'),
                config('schedule.type.jobop_available.value'),
                config('schedule.type.jobop_filled.value'),
            ])
            ->whereNotNull("event_id")
            ->whereInPeriod($start, $end)
            ->get();
    }

    public function getNotInGoogle(
        string $storeID,
        array $googleEventIDs,
        Carbon $start,
        Carbon $end,
        array $exceptGoogleAccounts = []
    ) {
        return Schedule::query()
            ->whereHas("store", function ($query) use ($exceptGoogleAccounts) {
                return $query->whereNotNull("google_account")
                    ->where("google_account", "not like", "%test%")
                    ->whereNotIn("google_account", $exceptGoogleAccounts);
            })
            ->where("store_id", $storeID)
            ->whereNotIn("type", [
                config('schedule.type.empty.value'),
                config('schedule.type.jobop_available.value'),
                config('schedule.type.jobop_filled.value'),
            ])
            ->where(function ($query) use ($googleEventIDs) {
                $query->whereNull("event_id")
                    ->orWhereNotIn("event_id", $googleEventIDs);
            })
            ->whereInPeriod($start, $end)
            ->get();
    }

    public function getBetweenPeriodByStoreID($storeID, $start, $end)
    {
        return Schedule::query()
            ->where("store_id", $storeID)
            ->whereInPeriod($start, $end)
            ->orderBy("start_date")
            ->get();
    }

    public function insert(array $schedules)
    {
        Schedule::insert($schedules);
    }

    public function updateByID(string $id, array $schedule)
    {
        Schedule::where("id", $id)->update($schedule);
    }

    public function deleteByIDS(array $ids)
    {
        Schedule::destroy($ids);
    }

    public function deleteEmptyBeforeNowByStoreID(string $storeID, Carbon $now)
    {
        Schedule::where("store_id", $storeID)
            ->where("type", config('schedule.type.empty.value'))
            ->where("end_date", "<=", $now)
            ->delete();
    }

    public function deleteEmptyInHoliday(string $start, string $end)
    {
        Schedule::query()
            ->where("type", config('schedule.type.empty.value'))
            ->whereInPeriod($start, $end)
            ->delete();
    }

    public function deleteOverlapEmptySchedule(string $storeID, string $start, string $end)
    {
        Schedule::query()
            ->where("store_id", $storeID)
            ->where("type", config('schedule.type.empty.value'))
            ->whereInPeriod($start, $end)
            ->delete();
    }
}
