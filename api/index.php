<?php

/**
 * Vercel Serverless Entrypoint for Laravel
 */

// Show detailed errors on Vercel for diagnosis
ini_set('display_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Fatal PHP Error:\n" . $error['message'] . "\nin " . $error['file'] . " on line " . $error['line'];
    }
});

// 1. Debug server variables
if (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '__debug_server')) {
    header('Content-Type: application/json');
    echo json_encode([
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
        'PATH_INFO' => $_SERVER['PATH_INFO'] ?? null,
        'PHP_SELF' => $_SERVER['PHP_SELF'] ?? null,
        'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? null,
        'HEADERS' => getallheaders(),
    ], JSON_PRETTY_PRINT);
    exit;
}

// 1. Prepare writable storage & cache directories inside /tmp
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath,
    $storagePath . '/app',
    $storagePath . '/framework',
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. Redirect all Laravel runtime cache & storage to /tmp
$serverlessEnv = [
    'LARAVEL_STORAGE_PATH' => $storagePath,
    'VIEW_COMPILED_PATH'   => $storagePath . '/framework/views',
    'APP_CONFIG_CACHE'     => $storagePath . '/framework/cache/config.php',
    'APP_EVENTS_CACHE'     => $storagePath . '/framework/cache/events.php',
    'APP_PACKAGES_CACHE'   => $storagePath . '/framework/cache/packages.php',
    'APP_ROUTES_CACHE'     => $storagePath . '/framework/cache/routes.php',
    'APP_SERVICES_CACHE'   => $storagePath . '/framework/cache/services.php',
];

foreach ($serverlessEnv as $k => $v) {
    $_ENV[$k] = $v;
    $_SERVER[$k] = $v;
    putenv("{$k}={$v}");
}

// 3. Forward request to Laravel public entrypoint
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Server Error in Laravel on Vercel:\n\n";
    echo $e->getMessage() . "\n\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
}
