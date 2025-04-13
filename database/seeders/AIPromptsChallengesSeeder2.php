<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeachingChallenge;
use App\Models\ChallengeExercise;
use App\Models\User;

class AIPromptsChallengesSeeder2 extends Seeder
{
    public function run(): void
    {
        // Obtener el profesor existente o crear uno nuevo
        $teacher = User::where('role', 'teacher')->first();
        if (!$teacher) {
            $teacher = User::create([
                'username' => 'prof_maria',
                'first_name' => 'María',
                'last_name' => 'López',
                'email' => 'maria.lopez@ejemplo.com',
                'password' => bcrypt('password123'),
                'role' => 'teacher',
                'is_active' => true,
                'date_registered' => now(),
            ]);
        }

        // Desafío 4: Historia - Investigación Histórica
        $challenge4 = TeachingChallenge::create([
            'title' => 'Viaje por la Historia con IA',
            'description' => 'Usa la IA para explorar eventos históricos importantes y entender mejor nuestro pasado.',
            'objectives' => 'Aprender a investigar eventos históricos usando la IA como herramienta de investigación y análisis.',
            'instructions' => 'En este desafío aprenderás a usar la IA para investigar y comprender mejor diferentes períodos históricos.',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'intermedio',
            'estimated_time' => 60,
            'points' => 120,
            'status' => 'published',
            'is_public' => true,
            'teacher_id' => $teacher->user_id,
        ]);

        $ejercicios4 = [
            [
                'title' => 'Línea del Tiempo Interactiva',
                'description' => 'Crea una línea del tiempo visual de un período histórico.',
                'instructions' => 'Usa la IA para generar una línea del tiempo detallada de un período histórico importante, incluyendo eventos clave y sus conexiones.',
                'order' => 1,
                'points' => 12,
            ],
            [
                'title' => 'Personajes Históricos',
                'description' => 'Investiga sobre figuras históricas importantes.',
                'instructions' => 'Pide a la IA que te ayude a crear una biografía detallada de un personaje histórico, incluyendo su impacto en la sociedad.',
                'order' => 2,
                'points' => 12,
            ],
            [
                'title' => 'Análisis de Causas y Consecuencias',
                'description' => 'Comprende las relaciones entre eventos históricos.',
                'instructions' => 'Utiliza la IA para analizar las causas y consecuencias de un evento histórico importante.',
                'order' => 3,
                'points' => 12,
            ],
            [
                'title' => 'Vida Cotidiana en el Pasado',
                'description' => 'Explora cómo era la vida en diferentes épocas.',
                'instructions' => 'Pide a la IA que describa detalladamente cómo era la vida cotidiana en una época histórica específica.',
                'order' => 4,
                'points' => 12,
            ],
            [
                'title' => 'Comparación de Sociedades',
                'description' => 'Compara diferentes civilizaciones o períodos históricos.',
                'instructions' => 'Usa la IA para comparar dos sociedades o períodos históricos diferentes, analizando sus similitudes y diferencias.',
                'order' => 5,
                'points' => 12,
            ],
        ];

        foreach ($ejercicios4 as $ejercicio) {
            ChallengeExercise::create(array_merge($ejercicio, ['challenge_id' => $challenge4->id]));
        }

        // Desafío 5: Geografía - Explorando el Mundo
        $challenge5 = TeachingChallenge::create([
            'title' => 'Geografía Mundial con IA',
            'description' => 'Explora el mundo y sus características geográficas usando la IA como guía.',
            'objectives' => 'Desarrollar conocimientos sobre geografía física y humana utilizando la IA como herramienta de investigación.',
            'instructions' => 'En este desafío usarás la IA para aprender sobre diferentes aspectos de la geografía mundial.',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'intermedio',
            'estimated_time' => 55,
            'points' => 120,
            'status' => 'published',
            'is_public' => true,
            'teacher_id' => $teacher->user_id,
        ]);

        $ejercicios5 = [
            [
                'title' => 'Climas del Mundo',
                'description' => 'Investiga sobre diferentes tipos de clima.',
                'instructions' => 'Usa la IA para crear una guía sobre los diferentes tipos de clima y su impacto en la vida de las personas.',
                'order' => 1,
                'points' => 12,
            ],
            [
                'title' => 'Recursos Naturales',
                'description' => 'Explora los recursos naturales y su distribución.',
                'instructions' => 'Pide a la IA que te ayude a analizar la distribución de recursos naturales en diferentes regiones del mundo.',
                'order' => 2,
                'points' => 12,
            ],
            [
                'title' => 'Población y Cultura',
                'description' => 'Estudia las características demográficas y culturales.',
                'instructions' => 'Utiliza la IA para investigar sobre la población y diversidad cultural de diferentes regiones.',
                'order' => 3,
                'points' => 12,
            ],
            [
                'title' => 'Problemas Ambientales',
                'description' => 'Analiza desafíos ambientales actuales.',
                'instructions' => 'Pide a la IA que te ayude a identificar y explicar problemas ambientales importantes en diferentes regiones.',
                'order' => 4,
                'points' => 12,
            ],
            [
                'title' => 'Geografía Económica',
                'description' => 'Explora las actividades económicas por región.',
                'instructions' => 'Usa la IA para analizar las principales actividades económicas de diferentes regiones y su relación con la geografía.',
                'order' => 5,
                'points' => 12,
            ],
        ];

        foreach ($ejercicios5 as $ejercicio) {
            ChallengeExercise::create(array_merge($ejercicio, ['challenge_id' => $challenge5->id]));
        }

        // Desafío 6: Literatura - Análisis Literario
        $challenge6 = TeachingChallenge::create([
            'title' => 'Análisis Literario con IA',
            'description' => 'Aprende a analizar textos literarios con la ayuda de la IA.',
            'objectives' => 'Desarrollar habilidades de análisis literario y comprensión de textos usando la IA como herramienta de apoyo.',
            'instructions' => 'En este desafío aprenderás a usar la IA para analizar y comprender mejor las obras literarias.',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'intermedio',
            'estimated_time' => 65,
            'points' => 120,
            'status' => 'published',
            'is_public' => true,
            'teacher_id' => $teacher->user_id,
        ]);

        $ejercicios6 = [
            [
                'title' => 'Análisis de Personajes',
                'description' => 'Explora la construcción de personajes literarios.',
                'instructions' => 'Usa la IA para analizar en profundidad los personajes de una obra literaria, incluyendo sus motivaciones y desarrollo.',
                'order' => 1,
                'points' => 12,
            ],
            [
                'title' => 'Temas y Símbolos',
                'description' => 'Identifica temas y simbolismo en la literatura.',
                'instructions' => 'Pide a la IA que te ayude a identificar y analizar los temas principales y el simbolismo en una obra literaria.',
                'order' => 2,
                'points' => 12,
            ],
            [
                'title' => 'Contexto Histórico',
                'description' => 'Comprende el contexto de las obras literarias.',
                'instructions' => 'Utiliza la IA para investigar y entender el contexto histórico y social de una obra literaria.',
                'order' => 3,
                'points' => 12,
            ],
            [
                'title' => 'Estilo y Técnicas Narrativas',
                'description' => 'Analiza el estilo de escritura del autor.',
                'instructions' => 'Pide a la IA que te ayude a identificar y analizar las técnicas narrativas y el estilo del autor.',
                'order' => 4,
                'points' => 12,
            ],
            [
                'title' => 'Interpretación y Crítica',
                'description' => 'Desarrolla tu propia interpretación crítica.',
                'instructions' => 'Usa la IA para desarrollar un análisis crítico personal de una obra literaria, argumentando tus interpretaciones.',
                'order' => 5,
                'points' => 12,
            ],
        ];

        foreach ($ejercicios6 as $ejercicio) {
            ChallengeExercise::create(array_merge($ejercicio, ['challenge_id' => $challenge6->id]));
        }
    }
} 