<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeachingChallenge;
use App\Models\ChallengeExercise;
use App\Models\User;

class AIPromptsChallengesSeeder3 extends Seeder
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

        // Desafío 7: Biología - Investigación Científica
        $challenge7 = TeachingChallenge::create([
            'title' => 'Biología Avanzada con IA',
            'description' => 'Explora conceptos avanzados de biología utilizando la IA como herramienta de investigación.',
            'objectives' => 'Desarrollar habilidades de investigación científica y comprensión de conceptos biológicos complejos.',
            'instructions' => 'En este desafío usarás la IA para investigar y comprender temas avanzados de biología.',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'avanzado',
            'estimated_time' => 70,
            'points' => 150,
            'status' => 'published',
            'is_public' => true,
            'teacher_id' => $teacher->user_id,
        ]);

        $ejercicios7 = [
            [
                'title' => 'Genética y ADN',
                'description' => 'Explora los conceptos fundamentales de la genética.',
                'instructions' => 'Usa la IA para crear explicaciones detalladas sobre conceptos genéticos como la replicación del ADN y las mutaciones.',
                'order' => 1,
                'points' => 15,
            ],
            [
                'title' => 'Sistemas del Cuerpo Humano',
                'description' => 'Investiga el funcionamiento de los sistemas corporales.',
                'instructions' => 'Pide a la IA que te ayude a crear un análisis detallado de cómo interactúan los diferentes sistemas del cuerpo.',
                'order' => 2,
                'points' => 15,
            ],
            [
                'title' => 'Evolución y Adaptación',
                'description' => 'Comprende los procesos evolutivos.',
                'instructions' => 'Utiliza la IA para investigar y explicar ejemplos específicos de adaptación y evolución en diferentes especies.',
                'order' => 3,
                'points' => 15,
            ],
            [
                'title' => 'Ecología y Ecosistemas',
                'description' => 'Analiza las relaciones en los ecosistemas.',
                'instructions' => 'Pide a la IA que te ayude a crear un modelo detallado de un ecosistema y sus interacciones.',
                'order' => 4,
                'points' => 15,
            ],
            [
                'title' => 'Biotecnología',
                'description' => 'Explora aplicaciones modernas de la biología.',
                'instructions' => 'Usa la IA para investigar y explicar diferentes aplicaciones de la biotecnología en la medicina y la agricultura.',
                'order' => 5,
                'points' => 15,
            ],
        ];

        foreach ($ejercicios7 as $ejercicio) {
            ChallengeExercise::create(array_merge($ejercicio, ['challenge_id' => $challenge7->id]));
        }

        // Desafío 8: Física - Conceptos Fundamentales
        $challenge8 = TeachingChallenge::create([
            'title' => 'Física con IA',
            'description' => 'Aprende conceptos de física utilizando la IA como tutor personal.',
            'objectives' => 'Desarrollar comprensión de conceptos físicos fundamentales y su aplicación en el mundo real.',
            'instructions' => 'En este desafío usarás la IA para explorar y entender conceptos de física.',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'avanzado',
            'estimated_time' => 75,
            'points' => 150,
            'status' => 'published',
            'is_public' => true,
            'teacher_id' => $teacher->user_id,
        ]);

        $ejercicios8 = [
            [
                'title' => 'Leyes del Movimiento',
                'description' => 'Explora las leyes de Newton.',
                'instructions' => 'Usa la IA para crear explicaciones y ejemplos prácticos de las leyes del movimiento de Newton.',
                'order' => 1,
                'points' => 15,
            ],
            [
                'title' => 'Energía y Trabajo',
                'description' => 'Comprende los conceptos de energía.',
                'instructions' => 'Pide a la IA que te ayude a explicar los diferentes tipos de energía y su transformación.',
                'order' => 2,
                'points' => 15,
            ],
            [
                'title' => 'Ondas y Sonido',
                'description' => 'Investiga las propiedades de las ondas.',
                'instructions' => 'Utiliza la IA para crear explicaciones sobre las características y comportamiento de las ondas.',
                'order' => 3,
                'points' => 15,
            ],
            [
                'title' => 'Electricidad y Magnetismo',
                'description' => 'Explora fenómenos electromagnéticos.',
                'instructions' => 'Pide a la IA que te ayude a entender la relación entre electricidad y magnetismo con ejemplos cotidianos.',
                'order' => 4,
                'points' => 15,
            ],
            [
                'title' => 'Física Moderna',
                'description' => 'Introducción a conceptos de física moderna.',
                'instructions' => 'Usa la IA para explorar conceptos básicos de física moderna como la relatividad y la física cuántica.',
                'order' => 5,
                'points' => 15,
            ],
        ];

        foreach ($ejercicios8 as $ejercicio) {
            ChallengeExercise::create(array_merge($ejercicio, ['challenge_id' => $challenge8->id]));
        }

        // Desafío 9: Química - Explorando la Materia
        $challenge9 = TeachingChallenge::create([
            'title' => 'Química con IA',
            'description' => 'Descubre los fundamentos de la química con ayuda de la IA.',
            'objectives' => 'Desarrollar comprensión de conceptos químicos básicos y su aplicación en la vida cotidiana.',
            'instructions' => 'En este desafío usarás la IA para explorar y entender conceptos fundamentales de química.',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'avanzado',
            'estimated_time' => 65,
            'points' => 150,
            'status' => 'published',
            'is_public' => true,
            'teacher_id' => $teacher->user_id,
        ]);

        $ejercicios9 = [
            [
                'title' => 'Estructura Atómica',
                'description' => 'Explora la composición de los átomos.',
                'instructions' => 'Usa la IA para crear explicaciones detalladas sobre la estructura atómica y los elementos químicos.',
                'order' => 1,
                'points' => 15,
            ],
            [
                'title' => 'Enlaces Químicos',
                'description' => 'Comprende cómo se unen los átomos.',
                'instructions' => 'Pide a la IA que te ayude a explicar los diferentes tipos de enlaces químicos y sus propiedades.',
                'order' => 2,
                'points' => 15,
            ],
            [
                'title' => 'Reacciones Químicas',
                'description' => 'Investiga diferentes tipos de reacciones.',
                'instructions' => 'Utiliza la IA para analizar y explicar diferentes tipos de reacciones químicas y sus aplicaciones.',
                'order' => 3,
                'points' => 15,
            ],
            [
                'title' => 'Química Orgánica',
                'description' => 'Explora compuestos orgánicos básicos.',
                'instructions' => 'Pide a la IA que te ayude a entender los fundamentos de la química orgánica y su importancia.',
                'order' => 4,
                'points' => 15,
            ],
            [
                'title' => 'Química en la Vida Diaria',
                'description' => 'Aplica conceptos químicos a situaciones cotidianas.',
                'instructions' => 'Usa la IA para identificar y explicar procesos químicos que ocurren en nuestra vida diaria.',
                'order' => 5,
                'points' => 15,
            ],
        ];

        foreach ($ejercicios9 as $ejercicio) {
            ChallengeExercise::create(array_merge($ejercicio, ['challenge_id' => $challenge9->id]));
        }
    }
} 