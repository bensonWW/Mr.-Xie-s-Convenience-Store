<?php

// Health check endpoint
http_response_code(200);
echo json_encode([
    'status' => 'healthy',
    'timestamp' => date('c'),
    'php_version' => PHP_VERSION,
]);
