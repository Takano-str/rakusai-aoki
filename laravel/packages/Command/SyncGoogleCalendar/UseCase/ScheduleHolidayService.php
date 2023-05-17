<?php

namespace Packages\Command\SyncGoogleCalendar\UseCase;

use Carbon\Carbon;
use Google\Service\Calendar\Event;
use Packages\Command\SyncGoogleCalendar\Infrastructure\ScheduleHolidayRepository;
use Packages\Command\SyncGoogleCalendar\Infrastructure\ScheduleRepository;

class ScheduleHolidayService
{
	public function __construct($calendar)
	{
		$this->calendar = $calendar;

		$this->scheduleHolidayRepository = new ScheduleHolidayRepository();
	}

	public function updateHolidayEvents()
    {
        $holidays = $this->getHolidayEvents();

        foreach ($holidays as $holiday) {
            if (!$this->scheduleHolidayRepository->isExistByGoogleEventID($holiday["id"])) {
                $formatedSchedule = $this->calendar->convertToScheduleFormat($holiday);
                $this->scheduleHolidayRepository->create($formatedSchedule);
            }
        }

    }

    private function getHolidayEvents()
    {
        $calendarID = "ja.japanese#holiday@group.v.calendar.google.com";
        $option = $this->calendar->getCalendarOption();

        return $this->calendar->listEventItems($calendarID, $option);
    }
}
