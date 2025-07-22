<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],  // Allow API and Sanctum CSRF cookie path
    'allowed_methods' => ['*'],  // Allow all HTTP methods
    'allowed_origins' => [
        'http://localhost:3000', 
        'http://127.0.0.1:3000',
       
    ],
    'allowed_headers' => ['*'],  // Allow all headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,  // Allow credentials (cookies, authorization tokens)
];

