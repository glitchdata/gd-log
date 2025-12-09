<?php

return [
    'providers' => [
        'google' => [
            'enabled' => (bool) env('OAUTH_GOOGLE_ENABLED', true),
        ],
        'meta' => [
            'enabled' => (bool) env('OAUTH_META_ENABLED', false),
        ],
    ],
];
