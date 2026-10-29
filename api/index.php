<?php

/**
 * Vercel Serverless Entrypoint for Laravel
 */

// 1. Prepare dynamic storage directory inside /tmp (writable in serverless)
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    mkdir($storagePath, 0755, true);
    mkdir($storagePath . '/app', 0755, true);
    mkdir($storagePath . '/framework/views', 0755, true);
    mkdir($storagePath . '/framework/cache', 0755, true);
    mkdir($storagePath . '/framework/sessions', 0755, true);
    mkdir($storagePath . '/logs', 0755, true);
}

// 2. Instruct Laravel to use /tmp/storage
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

// 3. Forward request to Laravel's public entrypoint
require __DIR__ . '/../public/index.php';

