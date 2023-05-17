<?php

namespace App\Services\Google;

use App\Models\Store;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Channel;
use Google\Service\Calendar\Event;

class CalendarService
{
	public function __construct(Client $client, Carbon $now, Carbon $until)
	{
		$this->calendar = new Calendar($client);
		$this->now = $now;
        $this->until = $until;
	}

	public function delete($calendarId, $eventId, $optParams = [])
	{
		return $this->calendar->events->delete($calendarId, $eventId, $optParams);
	}

	public function get($calendarId, $eventId, $optParams = [])
	{
		return $this->calendar->events->get($calendarId, $eventId, $optParams);
	}

	public function insert($calendarId, Event $postBody, $optParams = [])
	{
		return $this->calendar->events->insert($calendarId, $postBody, $optParams);
	}

	public function listEventItems($calendarId, $optParams = [])
	{
		$events = $this->calendar->events->listEvents($calendarId, $optParams);
		$items = $events->getItems();

		while (!empty($events->getNextPageToken())) {
			$optParams["pageToken"] = $events->getNextPageToken();
			$events = $this->calendar->events->listEvents($calendarId, $optParams);
			$items = array_merge($items, $events->getItems());
		}
		return $items;
	}

	public function update($calendarId, $eventId, Event $postBody, $optParams = [])
	{
		return $this->calendar->events->update($calendarId, $eventId, $postBody, $optParams);
	}

	public function getConvertEventToSchedule(array $store, Event $event)
	{
		$calendarTitle = mb_substr($event->summary, 0, 100);

		if (empty($calendarTitle)
			|| $event->start->date
			|| $event->end->date
		) {
			return [];
		}

		$type = $this->getScheduleType($event);
		
		return [
			'store_id'        => $store['id'],
			'event_id'        => $event->id,
			'start_date'      => (new Carbon($event->start->dateTime))->format('Y-m-d H:i:s'),
			'end_date'        => (new Carbon($event->end->dateTime))->format('Y-m-d H:i:s'),
			'title'           => $calendarTitle,
			'description'     => $event->description ?? "",
			'interview_venue' => $store['store_name'],
			'type'            => $type,
			'created_at'      => Carbon::now()->format('Y-m-d H:i:s'),
			'updated_at'      => Carbon::now()->format('Y-m-d H:i:s'),
		];
	}

	public function convertToScheduleFormat(Event $event): array
    {
        return [
			'event_id'   => $event->id,
			'title'      => $event->summary,
			'start_date' => Carbon::parse($event->start->date ?? $event->start->dateTime),
			'end_date'   => Carbon::parse($event->end->date ?? $event->end->dateTime),
        ];
    }

	public function getScheduleType(Event $event): int
	{
		if (strpos($event->summary, config('schedule.type.interview.title')) !== false) {
			return config('schedule.type.interview.value');
		}

		return config('schedule.type.filled.value');
	}

	public function getCalendarOption(): array
    {
        return [
            "timeMin"      => $this->now->toRfc3339String(),
            "timeMax"      => $this->until->toRfc3339String(),
            "maxResults"   => 100,
            "orderBy"      => "startTime",
            "singleEvents" => true,
        ];
    }
}
