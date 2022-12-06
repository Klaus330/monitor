<?php

return [
    'total_crawl_limit' => 5000,
    'timeout_limit' => 30,
    'allow_redirects' => true,
    'verify_ssf' => true,
    'delay_between_requests' => 150,
    'concurrency' => 1,
    'max_reponse_size' => 1024 * 1024,
    'parseable_types' => 'text/html,text/plain',
    'user_agent' => 'Oopsie.app',
    'schema' => 'https'
];
