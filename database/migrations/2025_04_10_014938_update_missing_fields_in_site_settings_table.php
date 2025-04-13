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
        Schema::table('site_settings', function (Blueprint $table) {
            // Comprobamos si existen los campos del Objetivo Educativo
            if (!Schema::hasColumn('site_settings', 'goal_title')) {
                $table->string('goal_title')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'goal_subtitle')) {
                $table->text('goal_subtitle')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'goal_students_target')) {
                $table->integer('goal_students_target')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'goal_year')) {
                $table->integer('goal_year')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'current_students')) {
                $table->integer('current_students')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'current_projects')) {
                $table->integer('current_projects')->nullable();
            }
            if (!Schema::hasColumn('site_settings', 'current_badges')) {
                $table->integer('current_badges')->nullable();
            }
        });

        // Actualizamos con los datos proporcionados por el usuario si existe algún registro
        if (DB::table('site_settings')->count() > 0) {
            DB::table('site_settings')->update([
                'goal_title' => 'Objetivo Educativo',
                'goal_subtitle' => 'Democratizar el acceso a la tecnología y a la inteligencia artificial, brindando a cada niño las mejores herramientas para prepararse para el futuro, sin importar su contexto. Porque creemos que el talento está en todos lados, pero las oportunidades no. Mitai Code llega para cambiar eso.',
                'goal_students_target' => 1000,
                'goal_year' => 2025,
                'current_students' => 60,
                'current_projects' => 8,
                'current_badges' => 23,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No eliminamos los campos ya que son esenciales para el funcionamiento
    }
};
