<?php

return [

    'connections' => [
        'default' => [
            'ashost'    => env('SAP_HOST', 'localhost'),
            'sysnr'     => env('SAP_SYSTEM', '00'),
            'lang'      => env('SAP_LANGUAGE', 'EN'),
            'client'    => env('SAP_CLIENT'),
            'user'      => env('SAP_USERNAME'),
            'passwd'    => env('SAP_PASSWORD'),
        ]
    ],

];