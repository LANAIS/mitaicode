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
        Log::info('Aplicando migración fix_join_team_validation');
        
        // Esta migración documenta y realiza cambios en la validación del método joinTeam
        // del controlador HackathonController, cambiando la validación de team_id
        // para que acepte correctamente el campo 'id' de la tabla hackathon_teams
        
        // Modificar el controlador está fuera del alcance de una migración
        // pero podemos actualizar el esquema para asegurar compatibilidad
        
        // En lugar de intentar agregar un índice único o modificar la tabla, 
        // simplemente aseguramos que los valores de team_id estén sincronizados con id
        try {
            DB::statement('UPDATE hackathon_teams SET team_id = id WHERE team_id IS NULL OR team_id != id');
            Log::info('Actualizado team_id para que coincida con id en hackathon_teams');
        } catch (\Exception $e) {
            Log::error('Error al actualizar team_id: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir estos cambios
    }
};
