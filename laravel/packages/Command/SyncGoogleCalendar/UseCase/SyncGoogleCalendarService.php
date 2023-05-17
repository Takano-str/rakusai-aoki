<?php

namespace Packages\Command\SyncGoogleCalendar\UseCase;

use App\Services\Google\CalendarService;
use App\Services\Google\ClientService;
use Carbon\Carbon;
use Packages\Command\SyncGoogleCalendar\Infrastructure\StoreRepository;
use Packages\Command\SyncGoogleCalendar\UseCase\ScheduleHolidayService;
use Packages\Command\SyncGoogleCalendar\UseCase\ScheduleService;

class SyncGoogleCalendarService
{
    public function __construct()
    {
        $this->now = Carbon::now("Asia/Tokyo");
        $this->until = $this->now->copy()
        ->addDays(config("calendar.calc.end.day"))
        ->setHours(config("calendar.calc.end.hour"));

        $cliendService = new ClientService();
        $client = $cliendService->getClient();
        $this->calendar = new CalendarService($client, $this->now, $this->until);

        $this->storeRepository = new StoreRepository();
        $this->scheduleService = new ScheduleService($this->calendar, $this->now, $this->until);
    }

    public function main()
    {
        $masterCalenderID = config("calendar.masterCalenderID");

        $this->updateHolidays();

        $stores = $this->storeRepository->getAll()->toArray();

        foreach ($stores as $store) {

            $storeID = $store["id"];
            $calendarID = $store["google_account"];

            if (empty($calendarID)) {
                \Log::info('Googleアカウントが無いためスキップ -> storeId:' . $storeID);
                continue;
            }

            $guestAccounts = json_decode($store["guest_account"], true);
            $guestAccounts[] = $calendarID;

            $this->scheduleService->deleteBeforeNowByStoreID($storeID);

            $schedulesInDB = $this->scheduleService->getNotEmptyByStoreID($storeID);
            $eventsInGoogle = $this->getEvents($calendarID);

            $this->scheduleService->syncGoogleEventsToDB($store, $schedulesInDB, $eventsInGoogle);

            $this->scheduleService->insertEventToGoogle(
                $storeID,
                $masterCalenderID,
                $eventsInGoogle,
                $guestAccounts
            );

            $this->scheduleService->insertEmptySchedule(
                $storeID,
                $store["store_name"],
            );

            $this->scheduleService->deleteEmptyInHoliday();
        }
    }

    private function updateHolidays()
    {
        $scheduleHolidayService = new ScheduleHolidayService($this->calendar);

        $scheduleHolidayService->updateHolidayEvents();
    }

    private function getEvents($calendarID)
    {
        $option = $this->calendar->getCalendarOption();

        return $this->calendar->listEventItems($calendarID, $option);
    }
}
