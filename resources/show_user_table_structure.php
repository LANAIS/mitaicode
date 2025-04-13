<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Obtener la información de la tabla users
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('users');

echo "Columnas de la tabla users:\n";
print_r($columns);

// Ver la estructura específica
foreach ($columns as $column) {
    $type = \Illuminate\Support\Facades\Schema::getColumnType('users', $column);
    echo "$column: $type\n";
}

?> 