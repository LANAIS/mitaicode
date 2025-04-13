<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo ejecutar si la tabla site_settings existe
        if (Schema::hasTable('site_settings')) {
            Schema::table('site_settings', function (Blueprint $table) {
                // Objetivo Educativo
                $table->string('goal_title')->nullable();
                $table->string('goal_subtitle')->nullable();
                $table->integer('goal_students_target')->nullable();
                $table->integer('goal_year')->nullable();
                $table->integer('current_students')->nullable();
                $table->integer('current_projects')->nullable();
                $table->integer('current_badges')->nullable();
                
                // Testimonios
                $table->string('testimonials_title')->nullable();
                $table->text('testimonial1_content')->nullable();
                $table->string('testimonial1_author')->nullable();
                $table->string('testimonial1_role')->nullable();
                $table->text('testimonial2_content')->nullable();
                $table->string('testimonial2_author')->nullable();
                $table->string('testimonial2_role')->nullable();
                $table->text('testimonial3_content')->nullable();
                $table->string('testimonial3_author')->nullable();
                $table->string('testimonial3_role')->nullable();
                
                // Sección de Registro
                $table->string('register_title')->nullable();
                $table->text('register_subtitle')->nullable();
                $table->string('student_label')->nullable();
                $table->string('teacher_label')->nullable();
                $table->string('register_button_text')->nullable();
                
                // Footer
                $table->text('footer_description')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('footer_copyright')->nullable();
            });
            
            // Insertar valores predeterminados
            DB::table('site_settings')->update([
                // Objetivo Educativo
                'goal_title' => 'Objetivo Educativo',
                'goal_subtitle' => 'Estamos en misión de enseñar programación a 10,000 niños en 2025',
                'goal_students_target' => 10000,
                'goal_year' => 2025,
                'current_students' => 3250,
                'current_projects' => 15780,
                'current_badges' => 42350,
                
                // Testimonios
                'testimonials_title' => 'Lo que dicen nuestros usuarios',
                'testimonial1_content' => 'Mi hija de 8 años se divierte mucho con Mitaí Code. Ha creado juegos simples y ahora está aprendiendo a controlar luces con Arduino.',
                'testimonial1_author' => 'Laura Martínez',
                'testimonial1_role' => 'Madre de estudiante',
                'testimonial2_content' => 'Como docente, la plataforma me facilita enseñar conceptos de programación a mis alumnos de primaria. El panel de seguimiento es muy útil.',
                'testimonial2_author' => 'Carlos Rodríguez',
                'testimonial2_role' => 'Profesor de Tecnología',
                'testimonial3_content' => '¡Me encanta el modo aventura! Cada vez que completo una misión, aprendo algo nuevo y gano insignias para mi perfil.',
                'testimonial3_author' => 'Martín, 10 años',
                'testimonial3_role' => 'Estudiante',
                
                // Sección de Registro
                'register_title' => 'Únete a Mitaí Code',
                'register_subtitle' => 'Regístrate para comenzar tu aventura de programación. Es gratis para estudiantes y tiene funcionalidades especiales para docentes.',
                'student_label' => 'Soy estudiante',
                'teacher_label' => 'Soy docente',
                'register_button_text' => 'Registrarse',
                
                // Footer
                'footer_description' => 'Plataforma educativa para enseñar programación a niños mediante bloques visuales y experiencias gamificadas.',
                'contact_email' => 'info@mitaicode.com',
                'contact_phone' => '+123 456 7890',
                'footer_copyright' => 'Mitaí Code. Todos los derechos reservados.',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Solo ejecutar si la tabla site_settings existe
        if (Schema::hasTable('site_settings')) {
            Schema::table('site_settings', function (Blueprint $table) {
                // Eliminar campos de Objetivo Educativo
                $table->dropColumn([
                    'goal_title',
                    'goal_subtitle',
                    'goal_students_target',
                    'goal_year',
                    'current_students',
                    'current_projects',
                    'current_badges',
                ]);
                
                // Eliminar campos de Testimonios
                $table->dropColumn([
                    'testimonials_title',
                    'testimonial1_content',
                    'testimonial1_author',
                    'testimonial1_role',
                    'testimonial2_content',
                    'testimonial2_author',
                    'testimonial2_role',
                    'testimonial3_content',
                    'testimonial3_author',
                    'testimonial3_role',
                ]);
                
                // Eliminar campos de Sección de Registro
                $table->dropColumn([
                    'register_title',
                    'register_subtitle',
                    'student_label',
                    'teacher_label',
                    'register_button_text',
                ]);
                
                // Eliminar campos de Footer
                $table->dropColumn([
                    'footer_description',
                    'contact_email',
                    'contact_phone',
                    'footer_copyright',
                ]);
            });
        }
    }
};
