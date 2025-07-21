<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],  // Allow API and Sanctum CSRF cookie path
    'allowed_methods' => ['*'],  // Allow all HTTP methods
    'allowed_origins' => [
        'http://localhost:3000',  // Your frontend URL (change this if you deploy elsewhere)
        // Add other domains here if you plan to have multiple frontend apps
    ],
    'allowed_headers' => ['*'],  // Allow all headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,  // Allow credentials (cookies, authorization tokens)
];

