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
        // Primero verificamos si existe la tabla
        if (Schema::hasTable('hackathon_team_user')) {
            // Verificamos si hay registros en la tabla intermedia
            $count = DB::table('hackathon_team_user')->count();
            
            if ($count > 0) {
                // Actualizamos las referencias para que team_id en la tabla intermedia apunte al id correcto en hackathon_teams
                DB::statement('
                    UPDATE hackathon_team_user htu
                    JOIN hackathon_teams ht ON htu.team_id = ht.id
                    SET htu.team_id = ht.id
                ');
            }
            
            // Intentamos simplemente recrear la tabla si es necesario
            try {
                // Verificamos si existe alguna restricción de clave extranjera en la tabla
                // En lugar de usar Doctrine, verificamos si podemos modificar la tabla directamente
                Schema::table('hackathon_team_user', function (Blueprint $table) {
                    // Añadimos la restricción de clave foránea para team_id
                    $table->foreign('team_id')
                          ->references('id')
                          ->on('hackathon_teams')
                          ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Si hay un error, lo registramos pero continuamos
                DB::statement("INSERT INTO `migration_logs` (`message`, `created_at`) VALUES ('Error al añadir restricción de clave foránea: " . addslashes($e->getMessage()) . "', NOW())");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir estos cambios ya que son correcciones
    }
};
