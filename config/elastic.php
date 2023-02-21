<?php
declare(strict_types=1);

use App\Models\SiteVisit;

return [
    'connection' => [
        'host' => env('ELASTIC_HOST', '127.0.0.1'),
        'port' => env('ELASTIC_PORT', 9200),
        'scheme' => env('ELASTIC_SCHEME', 'http'),
    ],

    'indices' => [
        'site_visit'=> [
            'index' => env('SITE_VISIT_INDEX', 'site_visit'),
        ],
    ],
];
