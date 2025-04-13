<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeachingChallengesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 5 desafíos de enseñanza de ejemplo
        $challenges = [
            [
                'title' => 'Introducción a la Programación con Python',
                'description' => 'Un desafío básico para aprender los fundamentos de Python',
                'objectives' => 'Conocer la sintaxis básica de Python, variables, y estructuras de control',
                'difficulty_level' => 'beginner',
                'status' => 'published',
                'is_public' => true,
                'duration_minutes' => 45,
                'points_awarded' => 100,
                'published_at' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(5)
            ],
            [
                'title' => 'Algoritmos y Estructuras de Datos',
                'description' => 'Aprende los algoritmos fundamentales y estructuras de datos',
                'objectives' => 'Implementar algoritmos de ordenamiento, búsqueda y estructuras como listas, pilas y colas',
                'difficulty_level' => 'intermediate',
                'status' => 'published',
                'is_public' => true,
                'duration_minutes' => 90,
                'points_awarded' => 150,
                'published_at' => Carbon::now()->subDays(3),
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(3)
            ],
            [
                'title' => 'Desarrollo Web con HTML, CSS y JavaScript',
                'description' => 'Construye tu primera página web interactiva',
                'objectives' => 'Crear una página web responsiva con HTML5, CSS3 y JavaScript',
                'difficulty_level' => 'beginner',
                'status' => 'published',
                'is_public' => true,
                'duration_minutes' => 60,
                'points_awarded' => 120,
                'published_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(2)
            ],
            [
                'title' => 'Inteligencia Artificial con TensorFlow',
                'description' => 'Introducción a los conceptos básicos de IA y redes neuronales',
                'objectives' => 'Implementar y entrenar un modelo de clasificación básico con TensorFlow',
                'difficulty_level' => 'advanced',
                'status' => 'draft',
                'is_public' => false,
                'duration_minutes' => 120,
                'points_awarded' => 200,
                'published_at' => null,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2)
            ],
            [
                'title' => 'Desarrollo de Aplicaciones Móviles con React Native',
                'description' => 'Aprende a crear aplicaciones móviles multiplataforma',
                'objectives' => 'Desarrollar y desplegar una aplicación móvil con React Native',
                'difficulty_level' => 'intermediate',
                'status' => 'published',
                'is_public' => false,
                'duration_minutes' => 90,
                'points_awarded' => 180,
                'published_at' => Carbon::now()->subDays(1),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(1)
            ],
        ];

        // Insertar los desafíos en la base de datos
        foreach ($challenges as $challenge) {
            DB::table('teaching_challenges')->insert($challenge);
        }
    }
}
