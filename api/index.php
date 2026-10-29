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

// Fix base path: Vercel serverless executes from api/index.php, which causes Symfony/Laravel
// to infer baseUrl as '/api'. Setting SCRIPT_NAME to '/index.php' ensures root is '/'
// so that normal web routes and '/api/*' routes are routed properly.
$_SERVER['SCRIPT_NAME'] = '/index.php';

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
