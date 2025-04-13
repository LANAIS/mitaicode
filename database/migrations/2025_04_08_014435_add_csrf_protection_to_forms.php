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
        // Verificar que la tabla site_settings existe
        if (Schema::hasTable('site_settings')) {
            // Comprobar la integridad de la tabla
            $isValid = DB::table('site_settings')->exists();
            
            if ($isValid) {
                echo "Tabla site_settings existe y contiene datos.\n";
                
                // Intentar reparar la tabla si es necesario
                try {
                    DB::statement('REPAIR TABLE site_settings');
                    echo "Tabla site_settings reparada.\n";
                } catch (\Exception $e) {
                    echo "No se pudo reparar la tabla: " . $e->getMessage() . "\n";
                }
                
                // Actualizar y verificar permisos de la tabla
                try {
                    DB::statement('OPTIMIZE TABLE site_settings');
                    echo "Tabla site_settings optimizada.\n";
                    
                    // Actualizar permisos (en MySQL/MariaDB)
                    DB::statement('FLUSH PRIVILEGES');
                    echo "Permisos actualizados.\n";
                } catch (\Exception $e) {
                    echo "Error en optimización: " . $e->getMessage() . "\n";
                }
                
                // Forzar una actualización para probar la escritura
                try {
                    // Primero probamos un registro simple
                    $testValue = 'Test-' . uniqid();
                    
                    $updated = DB::table('site_settings')
                        ->where('id', 1)
                        ->update([
                            'goal_subtitle' => $testValue,
                            'updated_at' => now()
                        ]);
                    
                    if ($updated) {
                        echo "Prueba de escritura exitosa: {$updated} filas actualizadas con valor de prueba '{$testValue}'.\n";
                    } else {
                        echo "No se pudo escribir en la tabla (0 filas afectadas).\n";
                    }
                    
                    // Verificamos si se guardó correctamente
                    $verificacion = DB::table('site_settings')
                        ->where('id', 1)
                        ->first();
                    
                    if ($verificacion && $verificacion->goal_subtitle === $testValue) {
                        echo "VERIFICACIÓN EXITOSA: El valor se guardó y recuperó correctamente.\n";
                    } else {
                        echo "ERROR DE VERIFICACIÓN: El valor guardado no coincide o no se pudo recuperar.\n";
                    }
                    
                    // Ahora probamos con el valor de students
                    $testStudents = 12345;
                    
                    $updatedStudents = DB::table('site_settings')
                        ->where('id', 1)
                        ->update([
                            'current_students' => $testStudents,
                            'updated_at' => now()
                        ]);
                    
                    if ($updatedStudents) {
                        echo "Prueba de escritura de estudiantes exitosa: {$updatedStudents} filas actualizadas con valor {$testStudents}.\n";
                        
                        // Verificamos el valor numérico
                        $verificacionNum = DB::table('site_settings')
                            ->where('id', 1)
                            ->first();
                        
                        if ($verificacionNum && $verificacionNum->current_students == $testStudents) {
                            echo "VERIFICACIÓN DE ESTUDIANTES EXITOSA: El valor numérico se guardó correctamente.\n";
                        } else {
                            echo "ERROR DE VERIFICACIÓN DE ESTUDIANTES: El valor numérico no coincide.\n";
                        }
                    } else {
                        echo "No se pudo actualizar el campo current_students.\n";
                    }
                    
                } catch (\Exception $e) {
                    echo "Error al escribir en la tabla: " . $e->getMessage() . "\n";
                }
            } else {
                echo "La tabla site_settings existe pero está vacía.\n";
                
                // Intentar crear un registro inicial
                try {
                    DB::table('site_settings')->insert([
                        'hero_title' => 'Sitio web en construcción',
                        'hero_subtitle' => 'Estamos trabajando para ofrecerte la mejor experiencia',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    echo "Registro inicial creado en site_settings.\n";
                } catch (\Exception $e) {
                    echo "Error al crear registro inicial: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "La tabla site_settings no existe.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacer nada en la reversión
    }
};
