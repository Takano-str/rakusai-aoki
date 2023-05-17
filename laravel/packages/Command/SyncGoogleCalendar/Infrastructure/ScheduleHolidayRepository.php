<?php

namespace Packages\Command\SyncGoogleCalendar\Infrastructure;

use App\Models\ScheduleHoliday;

class ScheduleHolidayRepository
{
	public function create($schedule)
	{
		ScheduleHoliday::create($schedule);
	}

	public function isExistByGoogleEventID($googleEventID)
	{
		return ScheduleHoliday::where("event_id", $googleEventID)->exists();
	}

	public function getBetweenPeriod($period)
	{
		return ScheduleHoliday::query()
		->whereBetween("start_date", $period)
		->whereBetween("end_date", $period)
		->orderBy("start_date")
		->get();
	}
}
