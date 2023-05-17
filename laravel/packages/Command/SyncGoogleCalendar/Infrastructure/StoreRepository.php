<?php

namespace Packages\Command\SyncGoogleCalendar\Infrastructure;

use App\Models\Store;

class StoreRepository
{
	public function getAll()
	{
		return Store::all();
	}
}