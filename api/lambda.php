<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Detect Vercel environment early
if (!isset($_ENV['VERCEL']) && !isset($_ENV['VERCEL_ENV'])) {
    // Check if we're running on Vercel by checking for Vercel-specific environment variables
    if (getenv('VERCEL') || getenv('VERCEL_ENV')) {
        $_ENV['VERCEL'] = getenv('VERCEL');
        $_ENV['VERCEL_ENV'] = getenv('VERCEL_ENV');
    }
}

// Determine if the application is in maintenance mode...
// Check both default and Vercel storage paths
$maintenancePath = __DIR__.'/../storage/framework/maintenance.php';
if (isset($_ENV['VERCEL']) || isset($_ENV['VERCEL_ENV']) || getenv('VERCEL') || getenv('VERCEL_ENV')) {
    $maintenancePath = '/tmp/storage/framework/maintenance.php';
}

if (file_exists($maintenancePath)) {
    require $maintenancePath;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
