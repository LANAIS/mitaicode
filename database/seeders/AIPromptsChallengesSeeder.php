<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeachingChallenge;
use App\Models\ChallengeExercise;
use App\Models\User;

class AIPromptsChallengesSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener o crear un profesor para los desafíos
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

        // Desafío 1: Lengua - Creación de Cuentos
        $challenge1 = TeachingChallenge::create([
            'title' => 'Creando Historias con IA',
            'description' => 'Aprende a usar la IA para crear historias creativas y mejorar tu escritura.',
            'objectives' => 'Desarrollar habilidades de escritura creativa, comprensión lectora y uso efectivo de prompts para generar historias.',
            'instructions' => 'En este desafío aprenderás a usar la IA para crear historias interesantes y mejorar tus habilidades de escritura.',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'principiante',
            'estimated_time' => 45,
            'points' => 100,
            'status' => 'published',
            'is_public' => true,
            'teacher_id' => $teacher->user_id,
        ]);

        $ejercicios1 = [
            [
                'title' => 'Creando un Personaje Principal',
                'description' => 'Usa la IA para crear un personaje interesante para tu historia.',
                'instructions' => 'Escribe un prompt que pida a la IA crear un personaje principal para una historia. Incluye características físicas, personalidad y algún detalle único.',
                'order' => 1,
                'points' => 10,
            ],
            [
                'title' => 'Diseñando el Escenario',
                'description' => 'Crea un mundo fascinante para tu historia.',
                'instructions' => 'Pide a la IA que te ayude a describir un escenario mágico o realista donde se desarrollará tu historia.',
                'order' => 2,
                'points' => 10,
            ],
            [
                'title' => 'Creando el Conflicto',
                'description' => 'Desarrolla un problema interesante para tu historia.',
                'instructions' => 'Utiliza la IA para generar un conflicto emocionante que tu personaje deberá resolver.',
                'order' => 3,
                'points' => 10,
            ],
            [
                'title' => 'Desarrollando Diálogos',
                'description' => 'Aprende a crear conversaciones naturales.',
                'instructions' => 'Pide a la IA que te ayude a escribir diálogos interesantes entre los personajes de tu historia.',
                'order' => 4,
                'points' => 10,
            ],
            [
                'title' => 'Final Sorprendente',
                'description' => 'Crea un final memorable para tu historia.',
                'instructions' => 'Usa la IA para generar diferentes opciones de final para tu historia y elige el más interesante.',
                'order' => 5,
                'points' => 10,
            ],
        ];

        foreach ($ejercicios1 as $ejercicio) {
            ChallengeExercise::create(array_merge($ejercicio, ['challenge_id' => $challenge1->id]));
        }

        // Desafío 2: Matemáticas - Resolución de Problemas
        $challenge2 = TeachingChallenge::create([
            'title' => 'Matemáticas Divertidas con IA',
            'description' => 'Descubre cómo la IA puede ayudarte a entender y resolver problemas matemáticos.',
            'objectives' => 'Aprender a usar la IA para comprender mejor los conceptos matemáticos y resolver problemas paso a paso.',
            'instructions' => 'En este desafío aprenderás a usar la IA como tu tutor personal de matemáticas.',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'principiante',
            'estimated_time' => 40,
            'points' => 100,
            'status' => 'published',
            'is_public' => true,
            'teacher_id' => $teacher->user_id,
        ]);

        $ejercicios2 = [
            [
                'title' => 'Entendiendo Fracciones',
                'description' => 'Usa la IA para visualizar y entender mejor las fracciones.',
                'instructions' => 'Pide a la IA que te explique el concepto de fracciones usando ejemplos de la vida real y visualizaciones.',
                'order' => 1,
                'points' => 10,
            ],
            [
                'title' => 'Problemas de Suma y Resta',
                'description' => 'Aprende a resolver problemas matemáticos paso a paso.',
                'instructions' => 'Utiliza la IA para que te ayude a resolver problemas de suma y resta, pidiendo que te explique cada paso.',
                'order' => 2,
                'points' => 10,
            ],
            [
                'title' => 'Geometría Básica',
                'description' => 'Explora las formas geométricas con ayuda de la IA.',
                'instructions' => 'Pide a la IA que te ayude a identificar y describir diferentes formas geométricas en objetos cotidianos.',
                'order' => 3,
                'points' => 10,
            ],
            [
                'title' => 'Multiplicación Divertida',
                'description' => 'Aprende las tablas de multiplicar de forma entretenida.',
                'instructions' => 'Usa la IA para crear juegos y trucos que te ayuden a memorizar las tablas de multiplicar.',
                'order' => 4,
                'points' => 10,
            ],
            [
                'title' => 'Problemas de la Vida Real',
                'description' => 'Aplica las matemáticas a situaciones cotidianas.',
                'instructions' => 'Pide a la IA que genere problemas matemáticos basados en situaciones reales que podrías encontrar en tu día a día.',
                'order' => 5,
                'points' => 10,
            ],
        ];

        foreach ($ejercicios2 as $ejercicio) {
            ChallengeExercise::create(array_merge($ejercicio, ['challenge_id' => $challenge2->id]));
        }

        // Desafío 3: Ciencias Naturales - Explorando la Naturaleza
        $challenge3 = TeachingChallenge::create([
            'title' => 'Explorando la Naturaleza con IA',
            'description' => 'Usa la IA para aprender sobre el mundo natural que nos rodea.',
            'objectives' => 'Desarrollar la curiosidad científica y aprender a usar la IA para investigar fenómenos naturales.',
            'instructions' => 'En este desafío aprenderás a usar la IA como tu asistente de investigación científica.',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'principiante',
            'estimated_time' => 50,
            'points' => 100,
            'status' => 'published',
            'is_public' => true,
            'teacher_id' => $teacher->user_id,
        ]);

        $ejercicios3 = [
            [
                'title' => 'Investigando Animales',
                'description' => 'Descubre datos fascinantes sobre diferentes animales.',
                'instructions' => 'Usa la IA para crear una ficha informativa sobre tu animal favorito, incluyendo su hábitat, alimentación y características únicas.',
                'order' => 1,
                'points' => 10,
            ],
            [
                'title' => 'El Ciclo del Agua',
                'description' => 'Comprende el ciclo del agua de forma interactiva.',
                'instructions' => 'Pide a la IA que te explique el ciclo del agua usando ejemplos creativos y analogías fáciles de entender.',
                'order' => 2,
                'points' => 10,
            ],
            [
                'title' => 'Sistema Solar',
                'description' => 'Explora los planetas y el espacio.',
                'instructions' => 'Utiliza la IA para crear una guía divertida sobre los planetas del sistema solar.',
                'order' => 3,
                'points' => 10,
            ],
            [
                'title' => 'Plantas y Fotosíntesis',
                'description' => 'Aprende cómo crecen las plantas.',
                'instructions' => 'Pide a la IA que te ayude a explicar el proceso de fotosíntesis como si fuera una historia divertida.',
                'order' => 4,
                'points' => 10,
            ],
            [
                'title' => 'Experimentos Caseros',
                'description' => 'Diseña experimentos seguros para hacer en casa.',
                'instructions' => 'Usa la IA para crear experimentos científicos sencillos que puedas realizar con materiales comunes.',
                'order' => 5,
                'points' => 10,
            ],
        ];

        foreach ($ejercicios3 as $ejercicio) {
            ChallengeExercise::create(array_merge($ejercicio, ['challenge_id' => $challenge3->id]));
        }
    }
} 