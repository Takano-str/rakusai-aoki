<?php

return [
	'view' => [
		'start' => [
			'day'  => 0,
			'hour' => 8,
		],
		'end' => [
			'day'  => 14,
			'hour' => 21,
		],
	],
	'calc' => [
		'start' => [
			'day'  => 0,
			'hour' => 10,
		],
		'end' => [
			'day'  => 14,
			'hour' => 20,
			// 'hour' => 18,
		],
	],
	'masterCalenderID' => env('MASTER_CALENDER_ID', 'media4mymc@gmail.com'),
];
