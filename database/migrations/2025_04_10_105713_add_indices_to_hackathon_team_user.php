<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Verificar si tenemos la estructura correcta
            $columns = Schema::getColumnListing('hackathon_team_user');
            
            // Log para debugging
            Log::info('Columnas en hackathon_team_user: ' . json_encode($columns));
            
            Schema::table('hackathon_team_user', function (Blueprint $table) use ($columns) {
                // Añadir/asegurar que existe is_leader si no existe
                if (!in_array('is_leader', $columns)) {
                    $table->boolean('is_leader')->default(false)->after('user_id');
                    Log::info('Columna is_leader añadida a hackathon_team_user');
                }
                
                // Eliminar column role si existe
                if (in_array('role', $columns)) {
                    $table->dropColumn('role');
                    Log::info('Columna role eliminada de hackathon_team_user');
                }
                
                // Verificar que tenemos los índices correctos para team_id y user_id
                // Esto puede lanzar errores si ya existen, por lo que lo colocamos en un try-catch
                try {
                    if (!Schema::hasColumn('hackathon_team_user', 'id')) {
                        // Si no tiene una PK, agregamos un ID autoincremental
                        $table->id()->first();
                        Log::info('Columna id añadida a hackathon_team_user');
                    }
                    
                    $table->index(['team_id', 'user_id']);
                    Log::info('Índice añadido para team_id y user_id en hackathon_team_user');
                } catch (\Exception $e) {
                    Log::info('Error al añadir índices: ' . $e->getMessage());
                    // Es probable que ya existan los índices
                }
            });
            
            // Intentamos crear las claves foráneas si no existen
            try {
                Schema::table('hackathon_team_user', function (Blueprint $table) {
                    // Primero comprobamos que las claves foráneas no existan ya
                    $foreignKeys = DB::select(
                        "SELECT * FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE TABLE_SCHEMA = DATABASE() 
                         AND TABLE_NAME = 'hackathon_team_user' 
                         AND REFERENCED_TABLE_NAME IS NOT NULL"
                    );
                    
                    $hasTeamFK = false;
                    $hasUserFK = false;
                    
                    foreach ($foreignKeys as $key) {
                        if ($key->COLUMN_NAME === 'team_id') {
                            $hasTeamFK = true;
                        }
                        if ($key->COLUMN_NAME === 'user_id') {
                            $hasUserFK = true;
                        }
                    }
                    
                    if (!$hasTeamFK) {
                        $table->foreign('team_id')->references('id')->on('hackathon_teams')->onDelete('cascade');
                        Log::info('FK añadida para team_id en hackathon_team_user');
                    }
                    
                    if (!$hasUserFK) {
                        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                        Log::info('FK añadida para user_id en hackathon_team_user');
                    }
                });
            } catch (\Exception $e) {
                Log::info('Error al añadir claves foráneas: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            Log::error('Error general en la migración: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertimos estos cambios para evitar problemas
    }
};
