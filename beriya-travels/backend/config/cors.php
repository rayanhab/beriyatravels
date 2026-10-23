<?php

$frontendOrigins = array_merge(
    explode(',', env('FRONTEND_URLS', 'http://localhost:5500,http://127.0.0.1:5500')),
    [env('FRONTEND_URL', 'http://localhost:4200')]
);

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
    'allowed_origins' => array_values(array_unique(array_filter(array_map('trim', $frontendOrigins)))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
