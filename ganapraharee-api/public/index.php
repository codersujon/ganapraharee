<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'code' => 200,
    'success' => true,
    'message' => 'Ganapraharee API is running',
    'data' => [
        'name' => 'Ganapraharee API',
        'version' => '1.0.0',
        'environment' => $_ENV['APP_ENV'] ?? 'production',
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
