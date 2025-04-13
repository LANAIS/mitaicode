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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            // Hero section
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('primary_button_text')->nullable();
            $table->string('primary_button_url')->nullable();
            $table->string('secondary_button_text')->nullable();
            $table->string('secondary_button_url')->nullable();
            
            // Features section - título de la sección
            $table->string('features_title')->nullable();
            
            // Feature 1 - Modo Aventura
            $table->string('feature1_title')->nullable();
            $table->text('feature1_description')->nullable();
            $table->string('feature1_icon')->nullable();
            
            // Feature 2 - Programación en Bloques
            $table->string('feature2_title')->nullable();
            $table->text('feature2_description')->nullable();
            $table->string('feature2_icon')->nullable();
            
            // Feature 3 - Control de Hardware
            $table->string('feature3_title')->nullable();
            $table->text('feature3_description')->nullable();
            $table->string('feature3_icon')->nullable();
            
            // Feature 4 - Plataforma para Docentes
            $table->string('feature4_title')->nullable();
            $table->text('feature4_description')->nullable();
            $table->string('feature4_icon')->nullable();
            
            // Feature 5 - Comunidad Colaborativa
            $table->string('feature5_title')->nullable();
            $table->text('feature5_description')->nullable();
            $table->string('feature5_icon')->nullable();
            
            // Feature 6 - Multiplataforma
            $table->string('feature6_title')->nullable();
            $table->text('feature6_description')->nullable();
            $table->string('feature6_icon')->nullable();
            
            $table->timestamps();
        });

        // Insertar valores por defecto
        DB::table('site_settings')->insert([
            // Hero section
            'hero_title' => '🧠✨ Aprender sobre IA hoy es crear el futuro de mañana',
            'hero_subtitle' => 'Mitaí code es un el ecosistema educativo gamificado e inteligente para niños. Hecho en Latinoamérica, para preparar a las nuevas generaciones para el mundo digital.',
            'logo_path' => 'assets/images/mitai-logo-512x512.svg',
            'primary_button_text' => 'Comenzar a programar',
            'primary_button_url' => '/',
            'secondary_button_text' => 'Modo Aventura',
            'secondary_button_url' => '/aventura',
            
            // Features section
            'features_title' => 'Descubre todas las posibilidades',
            
            // Feature 1 - Modo Aventura
            'feature1_title' => 'Modo Aventura',
            'feature1_description' => 'Supera misiones, gana recompensas y personaliza tu avatar mientras aprendes programación en un entorno gamificado.',
            'feature1_icon' => 'fas fa-gamepad',
            
            // Feature 2 - Programación en Bloques
            'feature2_title' => 'Programación en Bloques',
            'feature2_description' => 'Utiliza bloques visuales para crear programas, y observa el código real en Python o JavaScript para aprender sintaxis.',
            'feature2_icon' => 'fas fa-cubes',
            
            // Feature 3 - Control de Hardware
            'feature3_title' => 'Control de Hardware',
            'feature3_description' => 'Conecta con dispositivos reales como Arduino, ESP32 o Micro:bit para controlar robots, luces y sensores.',
            'feature3_icon' => 'fas fa-robot',
            
            // Feature 4 - Plataforma para Docentes
            'feature4_title' => 'Plataforma para Docentes',
            'feature4_description' => 'Crea aulas virtuales, asigna tareas y haz seguimiento del progreso de tus estudiantes con herramientas intuitivas.',
            'feature4_icon' => 'fas fa-chalkboard-teacher',
            
            // Feature 5 - Comunidad Colaborativa
            'feature5_title' => 'Comunidad Colaborativa',
            'feature5_description' => 'Comparte tus proyectos en un espacio seguro, comenta y modifica creaciones de otros estudiantes.',
            'feature5_icon' => 'fas fa-users',
            
            // Feature 6 - Multiplataforma
            'feature6_title' => 'Multiplataforma',
            'feature6_description' => 'Accede desde cualquier dispositivo con una interfaz adaptada a pantallas táctiles y simuladores incluidos.',
            'feature6_icon' => 'fas fa-mobile-alt',
            
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
}; 