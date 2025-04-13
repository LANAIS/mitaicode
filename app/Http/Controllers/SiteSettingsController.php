<?php

namespace App\Http\Controllers;

use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SiteSettingsController extends Controller
{
    // Lista de íconos disponibles para características
    private $availableIcons = [
        'fas fa-gamepad' => 'Videojuego / Gamepad',
        'fas fa-puzzle-piece' => 'Rompecabezas',
        'fas fa-code' => 'Código',
        'fas fa-laptop-code' => 'Programación',
        'fas fa-brain' => 'Pensamiento / Cerebro',
        'fas fa-graduation-cap' => 'Educación / Graduación',
        'fas fa-robot' => 'Robot / IA',
        'fas fa-lightbulb' => 'Idea / Inspiración',
        'fas fa-child' => 'Niño',
        'fas fa-users' => 'Usuarios / Comunidad',
        'fas fa-chart-line' => 'Crecimiento',
        'fas fa-trophy' => 'Logro / Trofeo',
        'fas fa-medal' => 'Medalla',
        'fas fa-star' => 'Estrella',
        'fas fa-rocket' => 'Cohete',
        'fas fa-flag-checkered' => 'Meta',
        'fas fa-book' => 'Libro',
        'fas fa-university' => 'Educación / Universidad',
        'fas fa-microscope' => 'Ciencia',
        'fas fa-flask' => 'Experimento',
        'fas fa-atom' => 'Átomo / Ciencia',
        'fas fa-cubes' => 'Bloques / Construcción',
        'fas fa-cogs' => 'Configuración / Engranajes',
        'fas fa-tools' => 'Herramientas',
        'fas fa-shield-alt' => 'Seguridad / Escudo',
        'fas fa-thumbs-up' => 'Me gusta',
        'fas fa-comments' => 'Comentarios',
        'fas fa-chalkboard-teacher' => 'Enseñanza',
        'fas fa-user-graduate' => 'Estudiante',
    ];

    /**
     * Show the form for editing the site settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        try {
            // Intentar obtener los settings directamente
            $settings = SiteSettings::first();
            
            if (!$settings) {
                Log::warning('No se encontró configuración existente - obteniendo valores por defecto');
                // Si no hay configuración, usar getSettings que creará una con valores por defecto
                $settings = SiteSettings::getSettings();
                Log::info('Configuración creada con ID: ' . $settings->id);
            } else {
                Log::info('Configuración existente encontrada con ID: ' . $settings->id);
            }
            
            // Registrar los valores clave que se mostrarán
            Log::info('Valores de configuración a mostrar:', [
                'current_students' => $settings->current_students,
                'goal_students_target' => $settings->goal_students_target
            ]);
            
            return view('admin.site-settings', [
                'settings' => $settings,
                'availableIcons' => $this->availableIcons
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar configuración: ' . $e->getMessage());
            
            // En caso de error, intentar con el método getSettings como respaldo
            $settings = SiteSettings::getSettings();
            
            return view('admin.site-settings', [
                'settings' => $settings,
                'availableIcons' => $this->availableIcons,
                'error' => 'Hubo un problema al cargar la configuración: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Actualiza la configuración del sitio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Eliminar cualquier caché antes de iniciar
        \Illuminate\Support\Facades\Cache::flush();
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        
        // Registrar información de la solicitud para depuración
        Log::info('Solicitud de actualización de SiteSettings recibida', [
            'method' => $request->method(),
            'url' => $request->url(),
            'ip' => $request->ip(),
            'current_students' => $request->input('current_students'),
            'goal_students_target' => $request->input('goal_students_target'),
            'goal_subtitle' => $request->input('goal_subtitle')
        ]);
        
        // Validar la solicitud
        $validated = $request->validate([
            // Hero section
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string',
            'primary_button_text' => 'required|string|max:50',
            'primary_button_url' => 'required|string|max:255',
            'secondary_button_text' => 'required|string|max:50',
            'secondary_button_url' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            
            // Features section
            'features_title' => 'required|string|max:255',
            
            // Features
            'feature1_title' => 'required|string|max:255',
            'feature1_description' => 'required|string',
            'feature1_icon' => 'required|string|max:50',
            
            'feature2_title' => 'required|string|max:255',
            'feature2_description' => 'required|string',
            'feature2_icon' => 'required|string|max:50',
            
            'feature3_title' => 'required|string|max:255',
            'feature3_description' => 'required|string',
            'feature3_icon' => 'required|string|max:50',
            
            'feature4_title' => 'required|string|max:255',
            'feature4_description' => 'required|string',
            'feature4_icon' => 'required|string|max:50',
            
            'feature5_title' => 'required|string|max:255',
            'feature5_description' => 'required|string',
            'feature5_icon' => 'required|string|max:50',
            
            'feature6_title' => 'required|string|max:255',
            'feature6_description' => 'required|string',
            'feature6_icon' => 'required|string|max:50',
            
            // Objetivo Educativo
            'goal_title' => 'required|string|max:255',
            'goal_subtitle' => 'required|string',
            'goal_students_target' => 'required|integer|min:1',
            'goal_year' => 'required|integer|min:2023|max:2050',
            'current_students' => 'required|integer|min:0',
            'current_projects' => 'required|integer|min:0',
            'current_badges' => 'required|integer|min:0',
            
            // Testimonios
            'testimonials_title' => 'required|string|max:255',
            'testimonial1_content' => 'required|string',
            'testimonial1_author' => 'required|string|max:100',
            'testimonial1_role' => 'required|string|max:100',
            'testimonial2_content' => 'required|string',
            'testimonial2_author' => 'required|string|max:100',
            'testimonial2_role' => 'required|string|max:100',
            'testimonial3_content' => 'required|string',
            'testimonial3_author' => 'required|string|max:100',
            'testimonial3_role' => 'required|string|max:100',
            
            // Registro
            'register_title' => 'required|string|max:255',
            'register_subtitle' => 'required|string',
            'student_label' => 'required|string|max:100',
            'teacher_label' => 'required|string|max:100',
            'register_button_text' => 'required|string|max:50',
            
            // Footer
            'footer_description' => 'required|string',
            'contact_email' => 'required|email|max:100',
            'contact_phone' => 'required|string|max:50',
            'footer_copyright' => 'required|string|max:255',
        ]);
        
        try {
            // Obtener configuración actual
            $settings = SiteSettings::first();
            
            if (!$settings) {
                Log::warning('No se encontró ninguna configuración - creando una nueva');
                $settings = new SiteSettings();
            }
            
            Log::info('Configuración obtenida/creada con ID: ' . ($settings->id ?? 'Nuevo registro'));
            
            // Datos específicos del Objetivo Educativo para depuración
            Log::info('Datos del Objetivo Educativo recibidos:', [
                'goal_title' => $validated['goal_title'],
                'goal_subtitle' => $validated['goal_subtitle'],
                'goal_students_target' => $validated['goal_students_target'],
                'goal_year' => $validated['goal_year'],
                'current_students' => $validated['current_students'],
                'current_projects' => $validated['current_projects'],
                'current_badges' => $validated['current_badges'],
            ]);
            
            // Actualizar los campos del modelo
            foreach ($validated as $key => $value) {
                if ($key !== 'logo') {
                    $settings->{$key} = $value;
                    // Log para cada campo actualizado
                    Log::info("Campo '{$key}' actualizado a: " . (is_numeric($value) ? $value : substr($value, 0, 30) . '...'));
                }
            }
            
            // Guardar los cambios
            $result = $settings->save();
            Log::info('Resultado del guardado: ' . ($result ? 'Éxito' : 'Fallo'));
            
            // Si el método eloquent falló, intentar actualizar directamente en la base de datos
            if (!$result) {
                Log::warning('Intento fallido de guardar con Eloquent, usando consulta directa');
                
                $data = collect($validated)->except('logo')->toArray();
                $data['updated_at'] = now();
                
                if ($settings->id) {
                    // Actualizar registro existente
                    DB::table('site_settings')->where('id', $settings->id)->update($data);
                } else {
                    // Insertar nuevo registro
                    $insertId = DB::table('site_settings')->insertGetId($data + ['created_at' => now()]);
                    Log::info('Nuevo registro creado con ID: ' . $insertId);
                }
                
                Log::info('Actualización directa con SQL completada');
                
                // Verificar que se actualizaron los campos del Objetivo Educativo
                $updatedSettings = DB::table('site_settings')->first();
                Log::info('Valores del Objetivo Educativo después de la actualización directa:', [
                    'goal_title' => $updatedSettings->goal_title ?? 'No existe',
                    'goal_subtitle' => $updatedSettings->goal_subtitle ?? 'No existe',
                    'goal_students_target' => $updatedSettings->goal_students_target ?? 'No existe',
                    'current_students' => $updatedSettings->current_students ?? 'No existe',
                ]);
            }
            
            // Procesar el logo si se ha subido uno nuevo
            if ($request->hasFile('logo')) {
                try {
                    // Verificar si existe un logo anterior y no es el predeterminado
                    if ($settings->logo_path && $settings->logo_path != 'assets/images/mitai-logo-512x512.svg') {
                        $oldLogoPath = str_replace('public/', '', $settings->logo_path);
                        if (Storage::disk('public')->exists($oldLogoPath)) {
                            Storage::disk('public')->delete($oldLogoPath);
                        }
                    }
                    
                    // Guardar el nuevo logo
                    $logoPath = $request->file('logo')->store('logos', 'public');
                    
                    // Actualizar la ruta del logo
                    $settings->logo_path = $logoPath;
                    $settings->save();
                    
                    Log::info('Logo actualizado correctamente: ' . $logoPath);
                } catch (\Exception $e) {
                    Log::error('Error al procesar el logo: ' . $e->getMessage());
                }
            }
            
            // Limpiar cachés nuevamente después de la actualización
            \Illuminate\Support\Facades\Cache::flush();
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            
            // Verificar y registrar los valores actualizados
            $updatedSettings = SiteSettings::find($settings->id);
            Log::info('Valores después de la actualización', [
                'id' => $updatedSettings->id,
                'current_students' => $updatedSettings->current_students,
                'goal_students_target' => $updatedSettings->goal_students_target
            ]);
            
            return redirect()->route('admin.site-settings.edit')
                ->with('success', 'La configuración del sitio se ha actualizado correctamente. 
                    Valores actualizados - Estudiantes actuales: ' . $updatedSettings->current_students . ', 
                    Meta: ' . $updatedSettings->goal_students_target . '
                    Se limpiaron todas las cachés del sistema para asegurar que los cambios sean visibles inmediatamente.');
            
        } catch (\Exception $e) {
            Log::error('Error al procesar actualización: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return redirect()->route('admin.site-settings.edit')
                ->with('error', 'Error al guardar la configuración: ' . $e->getMessage());
        }
    }
} 