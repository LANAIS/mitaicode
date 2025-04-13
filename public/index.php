<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Agregar captura global de errores de división por cero
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (strpos($errstr, 'Division by zero') !== false) {
        error_log("División por cero capturada en $errfile:$errline: $errstr");
        return true; // Prevenir que PHP maneje el error
    }
    // Dejar que PHP maneje otros errores
    return false;
}, E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$app->handleRequest(Request::capture());
