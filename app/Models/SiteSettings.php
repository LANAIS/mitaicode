<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SiteSettings extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<string>
     */
    protected $fillable = [
        // Hero section
        'hero_title',
        'hero_subtitle',
        'logo_path',
        'primary_button_text',
        'primary_button_url',
        'secondary_button_text',
        'secondary_button_url',
        
        // Features section
        'features_title',
        
        // Feature 1 - Modo Aventura
        'feature1_title',
        'feature1_description',
        'feature1_icon',
        
        // Feature 2 - Programación en Bloques
        'feature2_title',
        'feature2_description',
        'feature2_icon',
        
        // Feature 3 - Control de Hardware
        'feature3_title',
        'feature3_description',
        'feature3_icon',
        
        // Feature 4 - Plataforma para Docentes
        'feature4_title',
        'feature4_description',
        'feature4_icon',
        
        // Feature 5 - Comunidad Colaborativa
        'feature5_title',
        'feature5_description',
        'feature5_icon',
        
        // Feature 6 - Multiplataforma
        'feature6_title',
        'feature6_description',
        'feature6_icon',
        
        // Objetivo Educativo
        'goal_title',
        'goal_subtitle',
        'goal_students_target',
        'goal_year',
        'current_students',
        'current_projects',
        'current_badges',
        
        // Testimonios
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
        
        // Sección de Registro
        'register_title',
        'register_subtitle',
        'student_label',
        'teacher_label',
        'register_button_text',
        
        // Footer
        'footer_description',
        'contact_email',
        'contact_phone',
        'footer_copyright',
    ];

    /**
     * Obtiene la configuración del sitio.
     * Si no existe, crea un registro con la configuración predeterminada.
     *
     * @return self
     */
    public static function getSettings(): self
    {
        // Obtener el registro directamente de la base de datos, sin ningún tipo de caché
        try {
            // Primero, purgar cualquier caché que pueda existir
            Cache::forget('site_settings');
            
            // Usar una conexión directa a la base de datos
            $settings = DB::table('site_settings')
                ->whereRaw('1=1')
                ->first();
                
            if (!$settings) {
                throw new \Exception('No se encontró ninguna configuración');
            }
            
            // Convertir el objeto StdClass a un modelo SiteSettings
            $model = new self();
            $model->exists = true;
            $model->setRawAttributes((array)$settings, true);
            
            return $model;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::info('SiteSettings: Creando configuración predeterminada porque no existe. Error: ' . $e->getMessage());
            
            // Si falla la consulta o no hay datos, intentar con el método estándar
            $settings = self::withoutGlobalScopes()
                ->newQuery()
                ->first();
                
            if (!$settings) {
                $settings = self::create([
                    // Hero section
                    'hero_title' => '🧠✨ Aprender sobre IA hoy es crear el futuro de mañana',
                    'hero_subtitle' => 'Mitaí code es un el ecosistema educativo gamificado e inteligente para niños. Hecho en Latinoamérica, para preparar a las nuevas generaciones para el mundo digital.',
                    'logo_path' => 'assets/images/mitai-logo-512x512.svg',
                    'primary_button_text' => 'Comenzar a programar',
                    'primary_button_url' => '/',
                    'secondary_button_text' => 'Modo Aventura',
                    'secondary_button_url' => '/aventura',
                    
                    // Features section
                    'features_title' => 'Descubre todas las posibilidades',
                    
                    // Feature 1 - Modo Aventura
                    'feature1_title' => 'Modo Aventura',
                    'feature1_description' => 'Supera misiones, gana recompensas y personaliza tu avatar mientras aprendes programación en un entorno gamificado.',
                    'feature1_icon' => 'fas fa-gamepad',
                    
                    // Feature 2 - Programación en Bloques
                    'feature2_title' => 'Programación en Bloques',
                    'feature2_description' => 'Utiliza bloques visuales para crear programas, y observa el código real en Python o JavaScript para aprender sintaxis.',
                    'feature2_icon' => 'fas fa-cubes',
                    
                    // Feature 3 - Control de Hardware
                    'feature3_title' => 'Control de Hardware',
                    'feature3_description' => 'Conecta con dispositivos reales como Arduino, ESP32 o Micro:bit para controlar robots, luces y sensores.',
                    'feature3_icon' => 'fas fa-robot',
                    
                    // Feature 4 - Plataforma para Docentes
                    'feature4_title' => 'Plataforma para Docentes',
                    'feature4_description' => 'Crea aulas virtuales, asigna tareas y haz seguimiento del progreso de tus estudiantes con herramientas intuitivas.',
                    'feature4_icon' => 'fas fa-chalkboard-teacher',
                    
                    // Feature 5 - Comunidad Colaborativa
                    'feature5_title' => 'Comunidad Colaborativa',
                    'feature5_description' => 'Comparte tus proyectos en un espacio seguro, comenta y modifica creaciones de otros estudiantes.',
                    'feature5_icon' => 'fas fa-users',
                    
                    // Feature 6 - Multiplataforma
                    'feature6_title' => 'Multiplataforma',
                    'feature6_description' => 'Accede desde cualquier dispositivo con una interfaz adaptada a pantallas táctiles y simuladores incluidos.',
                    'feature6_icon' => 'fas fa-mobile-alt',
                    
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
            
            return $settings;
        }
    }
    
    /**
     * Actualiza la configuración del sitio.
     *
     * @param array $data
     * @return self
     */
    public static function updateSettings(array $data): self
    {
        // Obtener el registro actual o crear uno nuevo si no existe
        $settings = self::getSettings();
        
        // Debug - Imprimir valores antes de la actualización
        Log::info('SiteSettings - Valores antes de la actualización:', [
            'id' => $settings->id,
            'current_students_before' => $settings->current_students,
            'data_current_students' => $data['current_students']
        ]);
        
        try {
            // MÉTODO 1: Actualización usando Eloquent directamente
            $settings->fill($data);
            $settings->save();
            
            // Limpiar cachés
            Cache::forget('site_settings');
            Cache::flush();
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            
            // Obtener los datos actualizados para verificar
            $refreshed = self::find($settings->id);
            
            // Debug - Imprimir valores después de la actualización
            Log::info('SiteSettings - Valores después de la actualización:', [
                'id' => $refreshed->id,
                'current_students_after' => $refreshed->current_students
            ]);
            
            return $refreshed;
        } catch (\Exception $e) {
            Log::error('Error al actualizar SiteSettings: ' . $e->getMessage());
            
            // Forzar actualización con SQL directo como respaldo
            try {
                Log::info('Intentando actualización directa SQL como respaldo');
                $pdo = \DB::connection()->getPdo();
                $fields = [];
                $values = [];
                
                foreach ($data as $key => $value) {
                    $fields[] = "`{$key}` = ?";
                    $values[] = $value;
                }
                
                // Añadir updated_at
                $fields[] = "`updated_at` = ?";
                $values[] = date('Y-m-d H:i:s');
                
                // Añadir ID para WHERE
                $values[] = $settings->id;
                
                $sql = "UPDATE `site_settings` SET " . implode(', ', $fields) . " WHERE `id` = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
                
                // Verificar resultado
                Log::info('Actualización SQL directa completada. Filas afectadas: ' . $stmt->rowCount());
                
                // Refrescar el modelo
                $refreshed = self::find($settings->id);
                return $refreshed;
            } catch (\Exception $sqlEx) {
                Log::error('Error en actualización SQL directa: ' . $sqlEx->getMessage());
                throw $e; // Lanzar la excepción original
            }
        }
    }
} 