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
        Schema::table('hackathon_team_user', function (Blueprint $table) {
            // Verificar si existe la columna 'role'
            if (Schema::hasColumn('hackathon_team_user', 'role')) {
                // Respaldamos los datos existentes si hay algún valor en role
                $existingLeaders = DB::table('hackathon_team_user')
                    ->where('role', 'leader')
                    ->get(['team_id', 'user_id']);
                
                // Eliminar la columna 'role'
                $table->dropColumn('role');
                
                // Verificamos si no existe is_leader y la creamos
                if (!Schema::hasColumn('hackathon_team_user', 'is_leader')) {
                    $table->boolean('is_leader')->default(false)->after('user_id');
                }
                
                // Restauramos los líderes
                foreach ($existingLeaders as $leader) {
                    DB::table('hackathon_team_user')
                        ->where('team_id', $leader->team_id)
                        ->where('user_id', $leader->user_id)
                        ->update(['is_leader' => true]);
                }
            } 
            // Si no existe role pero tampoco is_leader, creamos is_leader
            else if (!Schema::hasColumn('hackathon_team_user', 'is_leader')) {
                $table->boolean('is_leader')->default(false)->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertimos estos cambios para mantener la consistencia
    }
};
