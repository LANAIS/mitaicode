<?php

// Cargar el entorno de Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Obtener las columnas de la tabla site_settings
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('site_settings');

echo "Columnas en la tabla site_settings:\n";
print_r($columns);

// Verificar si los campos de testimonios existen
$testimonialFields = [
    'testimonial1_content',
    'testimonial1_author',
    'testimonial1_role',
    'testimonial2_content',
    'testimonial2_author',
    'testimonial2_role',
    'testimonial3_content',
    'testimonial3_author',
    'testimonial3_role',
];

echo "\nVerificación de campos de testimonios:\n";
foreach ($testimonialFields as $field) {
    echo "$field: " . (\Illuminate\Support\Facades\Schema::hasColumn('site_settings', $field) ? "EXISTE" : "NO EXISTE") . "\n";
} 