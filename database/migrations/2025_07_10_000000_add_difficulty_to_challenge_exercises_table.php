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
        Schema::table('challenge_exercises', function (Blueprint $table) {
            if (!Schema::hasColumn('challenge_exercises', 'difficulty')) {
                $table->string('difficulty')->default('intermedio')->after('points');
            }
        });

        // Actualizar todos los ejercicios existentes para que tengan una dificultad predeterminada
        DB::table('challenge_exercises')
            ->whereNull('difficulty')
            ->update(['difficulty' => 'intermedio']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenge_exercises', function (Blueprint $table) {
            if (Schema::hasColumn('challenge_exercises', 'difficulty')) {
                $table->dropColumn('difficulty');
            }
        });
    }
}; 