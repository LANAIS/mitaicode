<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ForceUpdateSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el registro existente
        $settings = SiteSettings::first();
        
        if ($settings) {
            echo "Actualizando valores de configuración...\n";
            
            try {
                // Método 1: Actualizar usando Eloquent
                $settings->current_students = 777; // valor distintivo para verificar
                $settings->current_projects = 888;
                $settings->current_badges = 999;
                $settings->goal_subtitle = 'Estamos en misión de enseñar programación a 10,000 niños en 2025 (actualizado el ' . date('Y-m-d H:i:s') . ')';
                $settings->save();
                
                // Método 2: Actualizar usando Query Builder
                DB::table('site_settings')
                    ->where('id', $settings->id)
                    ->update([
                        'current_students' => 777,
                        'current_projects' => 888,
                        'current_badges' => 999,
                        'updated_at' => now()
                    ]);
                
                // Método 3: SQL directo para garantizar
                $pdo = DB::connection()->getPdo();
                $sql = "UPDATE site_settings SET 
                    current_students = 777, 
                    current_projects = 888, 
                    current_badges = 999, 
                    goal_subtitle = :subtitle,
                    updated_at = NOW() 
                    WHERE id = :id";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':subtitle' => 'Estamos en misión de enseñar programación a 10,000 niños en 2025 (actualizado con SQL directo)',
                    ':id' => $settings->id
                ]);
                
                echo "SQL Directo ejecutado. Filas afectadas: " . $stmt->rowCount() . "\n";
                
                // Verificar valores actualizados
                $updated = DB::table('site_settings')
                    ->where('id', $settings->id)
                    ->first();
                
                echo "Valores actualizados en la base de datos:\n";
                echo "- Estudiantes actuales: " . $updated->current_students . "\n";
                echo "- Proyectos: " . $updated->current_projects . "\n";
                echo "- Insignias: " . $updated->current_badges . "\n";
                echo "- Subtítulo: " . $updated->goal_subtitle . "\n";
            } 
            catch (\Exception $e) {
                echo "Error al actualizar configuración: " . $e->getMessage() . "\n";
                Log::error('Error en ForceUpdateSettingsSeeder: ' . $e->getMessage());
            }
            
            // Limpiar todo tipo de cachés
            echo "Limpiando caché...\n";
            Cache::flush();
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            
            echo "Configuración actualizada correctamente.\n";
        } else {
            echo "No se encontró la configuración del sitio.\n";
        }
    }
} 