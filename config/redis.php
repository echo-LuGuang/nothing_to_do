<?php

return [
    'default' => [
        'host' => getenv('REDIS_HOST'),
        'password' => getenv('REDIS_PASSWORD'),
        'port' => 6379,
        'database' => 9,
    ],
];
