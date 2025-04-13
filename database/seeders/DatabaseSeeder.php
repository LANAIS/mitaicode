<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Crear usuario administrador por defecto
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Sistema',
                'email' => 'admin@mitaicode.com',
                'password' => bcrypt('password123'),
                'role' => 'admin'
            ]
        );

        // Ejecutar el seeder de hackathones
        $this->call([
            HackathonSeeder::class,
            HackathonTeamSeeder::class,
            HackathonRoundSeeder::class
        ]);

        // Verificar si existen las tablas necesarias antes de ejecutar los demás seeders
        $tableExists = Schema::hasTable('categories');
        
        // Agregar categorías para lecciones de prompt engineering solo si la tabla existe
        if ($tableExists) {
            $this->createPromptCategories();
            
            // Ejecutar los demás seeders
            $this->call([
                DemoChallengesSeeder::class,
                StoreItemsSeeder::class,
                EmailNotificationSeeder::class,
                // Nuevos seeders de desafíos de IA
                AIPromptsChallengesSeeder::class,
                AIPromptsChallengesSeeder2::class,
                AIPromptsChallengesSeeder3::class,
                // Seeder para el asistente de IA
                AIAssistantPromptSeeder::class,
            ]);
        }

        $this->call([
            SiteSettingsSeeder::class,
        ]);
    }

    /**
     * Crea las categorías para lecciones de prompt engineering
     */
    private function createPromptCategories()
    {
        $categories = [
            [
                'name' => 'Introducción a la IA',
                'description' => 'Categoría para lecciones introductorias sobre inteligencia artificial y modelos de lenguaje',
                'icon' => 'fa-robot',
                'color' => '#4e73df'
            ],
            [
                'name' => 'Prompts Básicos',
                'description' => 'Aprende los conceptos básicos de la ingeniería de prompts',
                'icon' => 'fa-keyboard',
                'color' => '#1cc88a'
            ],
            [
                'name' => 'Prompts Avanzados',
                'description' => 'Técnicas avanzadas para crear prompts efectivos',
                'icon' => 'fa-magic',
                'color' => '#36b9cc'
            ],
            [
                'name' => 'Generación de Imágenes',
                'description' => 'Aprende a generar imágenes con IA usando prompts efectivos',
                'icon' => 'fa-image',
                'color' => '#f6c23e'
            ],
            [
                'name' => 'Codificación con IA',
                'description' => 'Aprende a usar IA para ayudarte en programación',
                'icon' => 'fa-code',
                'color' => '#e74a3b'
            ],
            [
                'name' => 'Ética en IA',
                'description' => 'Consideraciones éticas en el uso de inteligencia artificial',
                'icon' => 'fa-balance-scale',
                'color' => '#6f42c1'
            ]
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'icon' => $category['icon'],
                    'color' => $category['color']
                ]
            );
        }
    }
}
