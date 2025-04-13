<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\DB;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existe un registro
        $existingSettings = DB::table('site_settings')->first();
        
        $data = [
            // Hero section - Actualizado con contenido más atractivo
            'hero_title' => '🚀 Tecnología y programación al alcance de los niños',
            'hero_subtitle' => 'Mitaí Code: plataforma educativa que combina la creatividad, la inteligencia artificial y la programación visual para crear la próxima generación de innovadores latinoamericanos.',
            'logo_path' => 'assets/images/mitai-logo-512x512.svg',
            'primary_button_text' => 'Empieza tu aventura',
            'primary_button_url' => '/aventura',
            'secondary_button_text' => 'Ver proyectos',
            'secondary_button_url' => '/proyectos',
            
            // Features section - Títulos más impactantes
            'features_title' => 'Un mundo de posibilidades digitales',
            
            // Feature 1 - Modo Aventura
            'feature1_title' => 'Aventura Interactiva',
            'feature1_description' => 'Embárcate en un viaje de aprendizaje con misiones, desafíos y recompensas que hacen que programar sea una aventura emocionante.',
            'feature1_icon' => 'fas fa-gamepad',
            
            // Feature 2 - Programación en Bloques
            'feature2_title' => 'Bloques Intuitivos',
            'feature2_description' => 'Aprende a programar de forma visual arrastrando bloques de código. Visualiza cómo tus bloques se convierten en código real mientras aprendes.',
            'feature2_icon' => 'fas fa-puzzle-piece',
            
            // Feature 3 - Control de Hardware
            'feature3_title' => 'Controla el Mundo Real',
            'feature3_description' => 'Conecta tus creaciones virtuales con el mundo físico. Programa robots, luces LED y sensores con nuestras integraciones de hardware.',
            'feature3_icon' => 'fas fa-robot',
            
            // Feature 4 - Plataforma para Docentes
            'feature4_title' => 'Herramientas Educativas',
            'feature4_description' => 'Potencia tus clases con nuestro dashboard para docentes. Crea aulas virtuales, asigna proyectos y monitorea el progreso en tiempo real.',
            'feature4_icon' => 'fas fa-chalkboard-teacher',
            
            // Feature 5 - Comunidad Colaborativa
            'feature5_title' => 'Comunidad Creativa',
            'feature5_description' => 'Inspírate, comparte y colabora en un entorno seguro diseñado para fomentar la creatividad colectiva y el aprendizaje entre pares.',
            'feature5_icon' => 'fas fa-users',
            
            // Feature 6 - Multiplataforma
            'feature6_title' => 'Aprende Donde Quieras',
            'feature6_description' => 'Accede desde cualquier dispositivo - computadoras, tablets o móviles. Tu experiencia de aprendizaje te sigue donde vayas.',
            'feature6_icon' => 'fas fa-laptop-code',
            
            // Objetivo Educativo - Datos actualizados
            'goal_title' => 'Nuestra Misión Educativa',
            'goal_subtitle' => 'Estamos transformando la educación tecnológica en Latinoamérica con una meta ambiciosa',
            'goal_students_target' => 15000,
            'goal_year' => 2024,
            'current_students' => 6750,
            'current_projects' => 27890,
            'current_badges' => 84520,
            
            // Testimonios - Comentarios más auténticos
            'testimonials_title' => 'Voces de nuestra comunidad',
            'testimonial1_content' => 'Mi hijo ha descubierto su pasión por la tecnología gracias a Mitaí Code. Ver cómo progresa desde juegos simples hasta pequeños robots es increíble. ¡Su confianza y creatividad han crecido enormemente!',
            'testimonial1_author' => 'María González',
            'testimonial1_role' => 'Madre de Lucas, 9 años',
            'testimonial2_content' => 'Como docente, Mitaí Code ha revolucionado mis clases de tecnología. Los estudiantes están completamente comprometidos, y el sistema de seguimiento me permite identificar rápidamente quién necesita apoyo adicional.',
            'testimonial2_author' => 'Prof. Javier Mendoza',
            'testimonial2_role' => 'Escuela Técnica Regional',
            'testimonial3_content' => 'Nunca pensé que programar sería tan divertido. Hice un juego que mis amigos adoran y ahora estoy programando un robot con Arduino. ¡Mi sueño es crear tecnología que ayude a las personas!',
            'testimonial3_author' => 'Sofía, 11 años',
            'testimonial3_role' => 'Estudiante destacada',
            
            // Sección de Registro - Llamado a la acción más claro
            'register_title' => 'Únete a la revolución educativa',
            'register_subtitle' => 'Regístrate hoy y comienza a construir el futuro. Acceso gratuito para estudiantes y herramientas especializadas para educadores.',
            'student_label' => 'Quiero aprender (estudiante)',
            'teacher_label' => 'Quiero enseñar (docente)',
            'register_button_text' => 'Crear cuenta gratuita',
            
            // Footer - Información actualizada
            'footer_description' => 'Mitaí Code es la plataforma latinoamericana de referencia para la educación tecnológica, combinando programación visual, gamificación e inteligencia artificial para inspirar a la próxima generación de innovadores.',
            'contact_email' => 'contacto@mitaicode.com',
            'contact_phone' => '+595 981 123456',
            'footer_copyright' => 'Mitaí Code © ' . date('Y') . ' - Desarrollando el talento digital de Latinoamérica',
        ];
        
        if (!$existingSettings) {
            // Si no existe, creamos un nuevo registro
            SiteSettings::create($data);
            $this->command->info('✅ Configuración del sitio creada correctamente.');
        } else {
            // Si existe, actualizamos el registro existente
            DB::table('site_settings')->where('id', $existingSettings->id)->update($data);
            $this->command->info('✅ Configuración del sitio actualizada correctamente.');
        }
    }
} 