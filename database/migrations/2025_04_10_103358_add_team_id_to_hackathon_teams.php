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
        Schema::table('hackathon_teams', function (Blueprint $table) {
            // Verificar si la columna team_id ya existe
            if (!Schema::hasColumn('hackathon_teams', 'team_id')) {
                // Añadir columna team_id
                $table->unsignedBigInteger('team_id')->after('id')->nullable();
            }
        });

        // Actualizar todos los registros existentes para que team_id tenga el mismo valor que id
        DB::statement('UPDATE hackathon_teams SET team_id = id WHERE team_id IS NULL');
        
        // Ahora que hemos actualizado los valores, modificamos team_id para que sea UNIQUE
        Schema::table('hackathon_teams', function (Blueprint $table) {
            // Modificar team_id para que sea único
            $table->unique('team_id');
        });

        // Actualizar también la tabla hackathon_team_user si existe
        if (Schema::hasTable('hackathon_team_user')) {
            // Verificar si ahora hay referencias mapeando team_id a id en hackathon_teams
            DB::statement('
                UPDATE hackathon_team_user htu
                JOIN hackathon_teams ht ON htu.team_id = ht.id
                SET htu.team_id = ht.team_id
                WHERE ht.team_id IS NOT NULL
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No eliminamos la columna en la reversión para evitar problemas con datos existentes
        // En caso de ser necesario, se puede crear una migración específica para eliminarla
    }
};
