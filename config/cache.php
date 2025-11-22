<?php

return [
    'path' => dirname(__DIR__) . '/storage/cache',
    'ttl' => env('CACHE_TTL', 3600),
];
