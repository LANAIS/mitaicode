<?php

// Cargar el entorno de Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Obtener la configuración actual
$settings = \App\Models\SiteSettings::first();

if (!$settings) {
    echo "No se encontró ninguna configuración. Creando una nueva...\n";
    $settings = new \App\Models\SiteSettings();
}

// Actualizamos los campos que pueden ser nulos
$settings->testimonials_title = 'Lo que dicen nuestros usuarios';
$settings->testimonial1_content = 'Mi hija de 8 años se divierte mucho con Mitaí Code. Ha creado juegos simples y ahora está aprendiendo a controlar luces con Arduino.';
$settings->testimonial1_author = 'Laura Martínez';
$settings->testimonial1_role = 'Madre de estudiante';
$settings->testimonial2_content = 'Como docente, la plataforma me facilita enseñar conceptos de programación a mis alumnos de primaria. El panel de seguimiento es muy útil.';
$settings->testimonial2_author = 'Carlos Rodríguez';
$settings->testimonial2_role = 'Profesor de Tecnología';
$settings->testimonial3_content = '¡Me encanta el modo aventura! Cada vez que completo una misión, aprendo algo nuevo y gano insignias para mi perfil.';
$settings->testimonial3_author = 'Martín, 10 años';
$settings->testimonial3_role = 'Estudiante';

// Sección de Registro
$settings->register_title = 'Únete a Mitaí Code';
$settings->register_subtitle = 'Regístrate para comenzar tu aventura de programación. Es gratis para estudiantes y tiene funcionalidades especiales para docentes.';
$settings->student_label = 'Soy estudiante';
$settings->teacher_label = 'Soy docente';
$settings->register_button_text = 'Registrarse';

// Footer
$settings->footer_description = 'Plataforma educativa para enseñar programación a niños mediante bloques visuales y experiencias gamificadas.';
$settings->contact_email = 'info@mitaicode.com';
$settings->contact_phone = '+123 456 7890';
$settings->footer_copyright = 'Mitaí Code. Todos los derechos reservados.';

// Guardar los cambios
try {
    $settings->save();
    echo "Configuración actualizada correctamente.\n";
    
    // Limpiar cachés
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "Cachés limpiadas.\n";
} catch (\Exception $e) {
    echo "Error al guardar la configuración: " . $e->getMessage() . "\n";
}

// Mostrar valores actualizados
echo "\nValores actualizados:\n";
echo "- testimonial1_author: " . $settings->testimonial1_author . "\n";
echo "- register_title: " . $settings->register_title . "\n";
echo "- footer_copyright: " . $settings->footer_copyright . "\n"; 