<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
            Category::firstOrCreate(
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