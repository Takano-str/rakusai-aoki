<?php

return [

	'type' => [
		'empty' => [
			'name'    => 'empty',
			'value'   => 0,
			'title'   => '面接枠',
			'alias'   => 'free',
			'color'   => '#3490dc',
			'colorId' => 1,
			'front'   => 'free',
		],
		'interview' => [
			'name'    => 'interview',
			'value'   => 1,
			'title'   => '面接確定',
			'alias'   => 'buried',
			'color'   => '#ff1c1c',
			'colorId' => 11,
			'front'   => 'buried',
		],
		'filled' => [
			'name'    => 'filled',
			'value'   => 2,
			'title'   => '面接不可',
			'alias'   => 'buried',
			'color'   => '#8d9195',
			'colorId' => 0,
			'front'   => 'buried',
		],
		'jobop_available' => [
			'name'    => 'jobop_available',
			'value'   => 10,
			'title'   => '面接枠',
			'alias'   => 'buried',
			'color'   => '',
			'colorId' => 0,
			'front'   => 'buried',
		],
		'jobop_filled' => [
			'name'    => 'jobop_filled',
			'value'   => 11,
			'title'   => '面接不可',
			'alias'   => 'buried',
			'color'   => '',
			'colorId' => 0,
			'front'   => 'buried',
		],
	],

	'interval' => 30,
];
