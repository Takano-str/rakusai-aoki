<?php

namespace Packages\Command\SyncGoogleCalendar\UseCase;

use Carbon\Carbon;
use Google\Service\Calendar\Event;
use Packages\Command\SyncGoogleCalendar\Infrastructure\ScheduleHolidayRepository;
use Packages\Command\SyncGoogleCalendar\Infrastructure\ScheduleRepository;

class ScheduleService
{
    public function __construct($calendar, Carbon $now, Carbon $end)
    {
        $this->calendar = $calendar;
        $this->now = $now;
        $this->end = $end;

        $this->scheduleRepository = new ScheduleRepository();
        $this->scheduleHolidayRepository = new ScheduleHolidayRepository();
    }

    public function deleteBeforeNowByStoreID(string $storeID)
    {
        $this->scheduleRepository->deleteEmptyBeforeNowByStoreID($storeID, $this->now);
    }

    public function getNotEmptyByStoreID(string $storeID): array
    {
        return $this->scheduleRepository->getNotEmptyByStoreID(
            $storeID,
            $this->now,
            $this->end
        )->toArray();
    }

    public function insertEventToGoogle(
        string $storeID,
        string $calendarID,
        array $events,
        array $guestAccounts
    ) {
        $googleEventIDs = array_map(function ($event) {
            return $event->id;
        }, $events);

        $schedules = $this->scheduleRepository->getNotInGoogle(
            $storeID,
            $googleEventIDs,
            $this->now,
            $this->end
        )->toArray();

        $attendees = $this->getAttendeesForGoogle($guestAccounts);

        foreach ($schedules as $schedule) {
            $event = $this->getInsertEvent($schedule, $attendees);
            $event = $this->calendar->insert($calendarID, $event);

            $updateValue = ["event_id" => $event->id];
            $this->scheduleRepository->updateByID($schedule["id"], $updateValue);
        }
    }

    public function syncGoogleEventsToDB(array $store, array $schedules, array $events)
    {
        [
            $createSchedules,
            $updateSchedules,
            $deleteSchedules
        ] = $this->separateEventsToEachResource($store, $schedules, $events);

        $this->scheduleRepository->insert($createSchedules);
        $this->scheduleRepository->deleteByIDS($deleteSchedules);

        foreach ($updateSchedules as $id => $schedule) {
            $this->scheduleRepository->updateByID($id, $schedule);
        }
    }

    public function insertEmptySchedule(string $storeID, string $storeName)
    {

        $schedules = $this->scheduleRepository->getBetweenPeriodByStoreID(
            $storeID,
            $this->now,
            $this->end
        )->toArray();

        $insertDatas = $this->createEmptySchedule($storeID, $storeName, $schedules);
        $this->scheduleRepository->insert($insertDatas);
    }

    public function deleteEmptyInHoliday()
    {
        $start = $this->now->copy()->setHours(0)->setMinutes(0)->setSeconds(0);
        $end = $this->end->copy()->addDays(1)->setHours(0)->setMinutes(0)->setSeconds(0);
        $period = [$start, $end];
        $holidays = $this->scheduleHolidayRepository->getBetweenPeriod($period)->toArray();

        foreach ($holidays as $holiday) {
            $this->scheduleRepository->deleteEmptyInHoliday(
                $holiday["start_date"],
                $holiday["end_date"]
            );
        }
    }

    private function separateEventsToEachResource(array $store, array $schedules, array $events): array
    {
        $googleEventIDsInSchedule = array_column($schedules, "event_id");

        $createSchedules = [];
        $updateSchedules = [];
        $deleteSchedules = $schedules;

        foreach ($events as $event) {
            $formatedEvent = $this->calendar->getConvertEventToSchedule($store, $event);

            if (empty($formatedEvent)) {
                continue;
            }

            if (!in_array($formatedEvent["event_id"], $googleEventIDsInSchedule)) {
                $createSchedules[] = $formatedEvent;
            } else {
                $index = array_search($formatedEvent["event_id"], $googleEventIDsInSchedule);
                $schedule = $schedules[$index];

                $updateValue = $this->getUpdataValue($schedule, $formatedEvent);

                if (!empty($updateValue)) {
                    $updateSchedules[$schedule["id"]] = $updateValue;
                }

                unset($googleEventIDsInSchedule[$index]);
                unset($deleteSchedules[$index]);
            }


            $this->scheduleRepository->deleteOverlapEmptySchedule(
                $store["id"],
                $formatedEvent["start_date"],
                $formatedEvent["end_date"]
            );
        }
        $deleteSchedules = array_filter($deleteSchedules, function ($deleteSchedule) {
            return !empty($deleteSchedule["event_id"]);
        });

        $deleteSchedules = array_column($deleteSchedules, "id");

        return [$createSchedules, $updateSchedules, $deleteSchedules];
    }

    private function getInsertEvent(array $schedule, array $attendees): Event
    {
        $params = [
            "summary"     => $this->getEventTitle($schedule),
            "description" => $schedule["description"],
            "start"       => [
                "dateTime" => (new Carbon($schedule["start_date"], "Asia/Tokyo"))->toRfc3339String(),
                "timeZone" => "Asia/Tokyo",
            ],
            "end"         => [
                "dateTime" => (new Carbon($schedule["end_date"], "Asia/Tokyo"))->toRfc3339String(),
                "timeZone" => "Asia/Tokyo",
            ],
            "guestsCanModify" => true,
            "attendees" => $attendees,
        ];

        if ($schedule["type"] == config("schedule.type.filled.value")) {
            $params["colorId"] = config("schedule.type.filled.colorId");
        }
        if ($schedule["type"] == config("schedule.type.interview.value")) {
            $params["colorId"] = config("schedule.type.interview.colorId");
        }

        return new Event($params);
    }

    private function getEventTitle($schedule)
    {
        if (!empty($schedule["title"])) {
            return $schedule["title"];
        }

        if ($schedule["type"] == config("schedule.type.empty.value")) {
            return config("schedule.type.empty.title");
        }

        if ($schedule["type"] == config("schedule.type.interview.value")) {
            return config("schedule.type.interview.title");
        }

        if ($schedule["type"] == config("schedule.type.filled.value")) {
            return config("schedule.type.filled.title");
        }

        return null;
    }

    private function getUpdataValue(array $schedule, array $event): array
    {
        $compareSchedule = [
            $schedule["title"],
            $schedule["description"],
            $schedule["type"],
            $schedule["start_date"],
            $schedule["end_date"]
        ];

        $campareEvent = [
            $event["title"],
            $event["description"],
            $event["type"],
            $event["start_date"],
            $event["end_date"]
        ];

        if (empty(array_diff($compareSchedule, $campareEvent))) {
            return [];
        }

        return [
            "start_date"  => $event["start_date"],
            "end_date"    => $event["end_date"],
            "title"       => $event["title"],
            "description" => $event["description"],
            "type"        => $event["type"],
            "updated_at"  => $this->now->format("Y-m-d H:i:s"),
        ];
    }

    private function createEmptySchedule(
        string $storeID,
        string $storeName,
        array $schedules,
    ) {
        $insertDatas = [];

        $interval = config("schedule.interval");
        $start = null;
        $scheduleStart = $this->now;

        if (isset($schedules[0]["start_date"])) {
            $scheduleStart = Carbon::createFromFormat(
                "Y-m-d H:i:s",
                $schedules[0]["start_date"],
                "Asia/Tokyo"
            );
        }

        if (empty($schedules) || $this->now->lt($scheduleStart)) {
            $start = $this->getStartDate($this->now, $interval);
        }

        for ($index = 0; $index < count($schedules) + 1; $index++) {
            if ($index === 0 && empty($start)) {
                continue;
            }

            if ($index !== 0) {
                $start = $schedules[$index - 1]["end_date"];
                $start = Carbon::createFromFormat("Y-m-d H:i:s", $start, "Asia/Tokyo");
                $start = $this->getStartDate($start, $interval);
            }

            if ($index === count($schedules)) {
                $end = $this->end->copy()->setMinutes(0);
            } else {
                $end = $schedules[$index]["start_date"];
                $end = Carbon::createFromFormat("Y-m-d H:i:s", $end, "Asia/Tokyo");
                $end = $this->getEndDate($end, $interval);
            }

            while ($start->lt($end)) {
                if ($start->hour >= config("calendar.calc.end.hour")) {
                    $start->addWeekdays(1)->setHours(config("calendar.calc.start.hour"))->setMinutes(0);
                }

                $diffInMinutes = $start->diffInMinutes($end);
                if ($start->gt($end) || $diffInMinutes < $interval) {
                    break;
                }

                $insertDatas[] = [
                    "store_id"   => $storeID,
                    "start_date" => $start->format("Y-m-d H:i:s"),
                    "end_date"   => $start->addMinutes($interval)->format("Y-m-d H:i:s"),
                    "event_id" => "",
                    "title" => config('schedule.type.empty.title'),
                    "description" => "",
                    "interview_venue" => $storeName . '(web)',
                    "type" => config('schedule.type.empty.value'),
                    "created_at" => $this->now->format('Y-m-d H:i:s'),
                    "updated_at" => $this->now->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $insertDatas;
    }

    private function getStartDate(Carbon $date, int $interval)
    {
        $startDate = $date->copy();

        $startHour = config("calendar.calc.start.hour");

        $hour = $date->hour;
        if ($hour < $startHour) {
            $startDate->setHours($startHour)->setMinutes(0);
        }

        $minute = $startDate->minute;
        $addMinute = intval(ceil($minute / $interval) * $interval) - $minute;

        $startDate->addMinutes($addMinute)->setSeconds(0);

        return $startDate;
    }

    private function getEndDate(Carbon $date, int $interval)
    {
        $minute = $date->minute;
        $subMinute = intval(ceil($minute % $interval));

        $endDate = $date->copy()->subMinutes($subMinute)->setSeconds(0);

        return $endDate;
    }

    private function getAttendeesForGoogle(array $guestAccounts)
    {
        $attendees = [];
        foreach ($guestAccounts as $guestAccount) {
            $attendee = [
                "email" => $guestAccount,
            ];
            $attendees[] = $attendee;
        }

        return $attendees;
    }
}
