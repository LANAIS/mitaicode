<?php

namespace App\Http\Controllers;

use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Muestra la página principal del sitio.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Forzar consulta fresca a la base de datos sin caché
        try {
            $settings = \Illuminate\Support\Facades\DB::table('site_settings')
                ->where('id', 1)
                ->first();
            
            if (!$settings) {
                // Si no hay configuración, obtener del modelo que creará los valores por defecto
                $settings = \App\Models\SiteSettings::getSettings();
            } else {
                // Convertir el objeto StdClass a un modelo para mantener compatibilidad
                $model = new \App\Models\SiteSettings();
                $model->exists = true;
                $model->setRawAttributes((array)$settings, true);
                $settings = $model;
            }
        } catch (\Exception $e) {
            // En caso de error, usar el método estándar
            \Illuminate\Support\Facades\Log::error('Error al obtener settings en HomeController: ' . $e->getMessage());
            $settings = \App\Models\SiteSettings::getSettings();
        }
        
        // Registro para debug
        \Illuminate\Support\Facades\Log::info('HomeController - Valores mostrados:', [
            'current_students' => $settings->current_students,
            'goal_students_target' => $settings->goal_students_target,
            'goal_subtitle' => $settings->goal_subtitle
        ]);
        
        return view('welcome', compact('settings'));
    }
} 