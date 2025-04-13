<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('site_settings')) {
            Schema::table('site_settings', function (Blueprint $table) {
                // Título general de la sección de características
                if (!Schema::hasColumn('site_settings', 'features_title')) {
                    $table->string('features_title')->default('Descubre todas las posibilidades');
                }
                
                // Característica 1
                if (!Schema::hasColumn('site_settings', 'feature1_title')) {
                    $table->string('feature1_title')->default('Modo Aventura');
                }
                if (!Schema::hasColumn('site_settings', 'feature1_description')) {
                    $table->text('feature1_description')->default('Aprende programación a través de divertidas misiones y desafíos.');
                }
                if (!Schema::hasColumn('site_settings', 'feature1_icon')) {
                    $table->string('feature1_icon')->default('fas fa-gamepad');
                }
                
                // Característica 2
                if (!Schema::hasColumn('site_settings', 'feature2_title')) {
                    $table->string('feature2_title')->default('Programación en Bloques');
                }
                if (!Schema::hasColumn('site_settings', 'feature2_description')) {
                    $table->text('feature2_description')->default('Interfaz visual e intuitiva para comenzar a programar sin escribir código.');
                }
                if (!Schema::hasColumn('site_settings', 'feature2_icon')) {
                    $table->string('feature2_icon')->default('fas fa-puzzle-piece');
                }
                
                // Característica 3
                if (!Schema::hasColumn('site_settings', 'feature3_title')) {
                    $table->string('feature3_title')->default('Proyectos Prácticos');
                }
                if (!Schema::hasColumn('site_settings', 'feature3_description')) {
                    $table->text('feature3_description')->default('Crea juegos, animaciones y aplicaciones mientras aprendes los fundamentos.');
                }
                if (!Schema::hasColumn('site_settings', 'feature3_icon')) {
                    $table->string('feature3_icon')->default('fas fa-rocket');
                }
                
                // Característica 4
                if (!Schema::hasColumn('site_settings', 'feature4_title')) {
                    $table->string('feature4_title')->default('Transición a Código Real');
                }
                if (!Schema::hasColumn('site_settings', 'feature4_description')) {
                    $table->text('feature4_description')->default('Pasa gradualmente de bloques a Python, JavaScript y otros lenguajes.');
                }
                if (!Schema::hasColumn('site_settings', 'feature4_icon')) {
                    $table->string('feature4_icon')->default('fas fa-code');
                }
                
                // Característica 5
                if (!Schema::hasColumn('site_settings', 'feature5_title')) {
                    $table->string('feature5_title')->default('Seguimiento de Progreso');
                }
                if (!Schema::hasColumn('site_settings', 'feature5_description')) {
                    $table->text('feature5_description')->default('Sistema inteligente que se adapta al ritmo y estilo de aprendizaje de cada niño.');
                }
                if (!Schema::hasColumn('site_settings', 'feature5_icon')) {
                    $table->string('feature5_icon')->default('fas fa-chart-line');
                }
                
                // Característica 6
                if (!Schema::hasColumn('site_settings', 'feature6_title')) {
                    $table->string('feature6_title')->default('Comunidad de Aprendizaje');
                }
                if (!Schema::hasColumn('site_settings', 'feature6_description')) {
                    $table->text('feature6_description')->default('Conecta con otros estudiantes, comparte proyectos y participa en desafíos.');
                }
                if (!Schema::hasColumn('site_settings', 'feature6_icon')) {
                    $table->string('feature6_icon')->default('fas fa-users');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('site_settings')) {
            Schema::table('site_settings', function (Blueprint $table) {
                // Eliminar todos los campos añadidos (sólo si existen)
                $columns = [
                    'features_title',
                    'feature1_title', 'feature1_description', 'feature1_icon',
                    'feature2_title', 'feature2_description', 'feature2_icon',
                    'feature3_title', 'feature3_description', 'feature3_icon',
                    'feature4_title', 'feature4_description', 'feature4_icon',
                    'feature5_title', 'feature5_description', 'feature5_icon',
                    'feature6_title', 'feature6_description', 'feature6_icon',
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('site_settings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
