<?php

return [
    // AWS lambda functions.
    'function' => [
        'common-mail' => [
            'to_address',
            'cc_address',
            'bcc_address',
            'from_address',
            'subject',
            'message',
        ],
    ],

    // tsuchiya domain functions.
    'interview' => [
        'from_address' => env('COMPANY_MAIL', 'nx-careerroad@rakusai.sendae.me'),
        'subject'      => '面接確定のお知らせ',
        'bcc_address'   => [
            'akiyama-d@dym.jp',
        ],
    ],
    'store_adjust' => [
        'from_address' => env('COMPANY_MAIL', 'nx-careerroad@rakusai.sendae.me'),
        'subject'      => '【至急対応】すぐに面接の日程調整を行ってください。',
        'bcc_address'   => [
            'akiyama-d@dym.jp',
        ],
    ],


    // 'sendMailByWorkSheetAnswered' => [
    //     'from_address' => env('COMPANY_MAIL'),
    //     'subject'      => '面接確定のお知らせ',
    //     'bcc_address'   => [
    //         'akiyama-d@dym.jp',
    //     ],
    // ],
    // 'sendMailByWorkSheetAnsweredAdjustment' => [
    //     'from_address' => env('COMPANY_MAIL'),
    //     'subject'      => '【至急対応】すぐに面接の日程調整を行ってください。',
    //     'cc_address'   => [
    //         'akiyama-d@dym.jp',
    //     ],
    // ],
    // 'sendMailByAddConsumer' => [
    //     'from_address' => env('COMPANY_MAIL'),
    //     'subject'      => '選考のご案内',
    //     'cc_address'   => [],
    // ],
    // 'sendMailByAddConsumer' => [
    //     'from_address' => env('COMPANY_MAIL'),
    //     'subject'      => '選考のご案内',
    //     'cc_address'   => [],
    // ],
    // 'sendMailByWorksheetCancel' => [
    //     'from_address' => env('COMPANY_MAIL'),
    //     'subject'      => '{{consumer_name}}様が面接をキャンセルされました',
    //     'cc_address'   => [],
    // ],
];
