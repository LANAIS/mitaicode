<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Classroom;
use App\Models\TeachingChallenge;
use App\Models\ChallengeExercise;
use App\Models\ChallengeStudentProgress;
use App\Models\ExerciseSubmission;
use Carbon\Carbon;

class DemoChallengesSeeder extends Seeder
{
    /**
     * Ejecuta los seeders.
     */
    public function run()
    {
        // Usar un timestamp para generar nombres de usuario únicos
        $timestamp = time();
        
        // Crear profesor de ejemplo
        $teacher = User::create([
            'username' => 'prof_ana_' . $timestamp,
            'first_name' => 'Ana',
            'last_name' => 'García',
            'email' => 'ana.garcia' . $timestamp . '@ejemplo.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
            'is_active' => true,
            'date_registered' => now(),
        ]);

        // Crear estudiantes de ejemplo
        $student1 = User::create([
            'username' => 'carlos_m_' . $timestamp,
            'first_name' => 'Carlos',
            'last_name' => 'Martínez',
            'email' => 'carlos' . $timestamp . '@ejemplo.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => true,
            'date_registered' => now(),
        ]);

        $student2 = User::create([
            'username' => 'lucia_f_' . $timestamp,
            'first_name' => 'Lucía',
            'last_name' => 'Fernández',
            'email' => 'lucia' . $timestamp . '@ejemplo.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => true,
            'date_registered' => now(),
        ]);

        $student3 = User::create([
            'username' => 'miguel_t_' . $timestamp,
            'first_name' => 'Miguel',
            'last_name' => 'Torres',
            'email' => 'miguel' . $timestamp . '@ejemplo.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_active' => true,
            'date_registered' => now(),
        ]);

        // Crear una clase para el profesor
        $classroom = Classroom::create([
            'class_name' => 'Fundamentos de IA y Programación',
            'teacher_id' => $teacher->user_id,
            'description' => 'Clase introductoria sobre inteligencia artificial y programación en Python',
            'is_active' => true,
            'class_code' => 'AI' . substr($timestamp, -7),
        ]);

        // Inscribir estudiantes en la clase
        DB::table('class_enrollments')->insert([
            ['class_id' => $classroom->class_id, 'student_id' => $student1->user_id, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['class_id' => $classroom->class_id, 'student_id' => $student2->user_id, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['class_id' => $classroom->class_id, 'student_id' => $student3->user_id, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 1. Crear un desafío de Python
        $pythonChallenge = TeachingChallenge::create([
            'title' => 'Fundamentos de Python: Estructuras de Datos',
            'description' => 'En este desafío aprenderás a trabajar con listas, diccionarios y otras estructuras de datos en Python.',
            'objectives' => 'Entender cómo funcionan las listas y diccionarios. Aprender a manipular datos estructurados. Resolver problemas utilizando estructuras de datos.',
            'instructions' => 'Completa los ejercicios propuestos utilizando los conceptos de estructuras de datos de Python. Asegúrate de probar tu código antes de enviarlo.',
            'teacher_id' => $teacher->user_id,
            'class_id' => $classroom->class_id,
            'is_public' => false,
            'status' => 'published',
            'challenge_type' => 'python',
            'difficulty' => 'intermedio',
            'estimated_time' => 60,
            'points' => 100,
            'order' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Ejercicios para el desafío de Python
        $pythonExercise1 = ChallengeExercise::create([
            'challenge_id' => $pythonChallenge->id,
            'title' => 'Trabajando con Listas',
            'description' => 'Aprende a manipular listas en Python',
            'instructions' => 'Crea una función llamada "procesar_lista" que reciba una lista de números y devuelva otra lista con los números pares multiplicados por 2 y los impares multiplicados por 3. Recuerda que puedes usar el operador % para determinar si un número es par o impar.',
            'order' => 1,
            'initial_code' => "def procesar_lista(numeros):\n    # Tu código aquí\n    resultado = []\n    \n    return resultado\n\n# Prueba tu función\nprint(procesar_lista([1, 2, 3, 4, 5]))",
            'points' => 30,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $pythonExercise2 = ChallengeExercise::create([
            'challenge_id' => $pythonChallenge->id,
            'title' => 'Diccionarios en Python',
            'description' => 'Aprende a trabajar con diccionarios',
            'instructions' => 'Crea una función llamada "contar_ocurrencias" que reciba una lista de palabras y devuelva un diccionario donde las claves sean las palabras y los valores sean el número de veces que aparece cada palabra. Puedes usar el método .get() del diccionario para obtener un valor por defecto si la clave no existe.',
            'order' => 2,
            'initial_code' => "def contar_ocurrencias(palabras):\n    # Tu código aquí\n    resultado = {}\n    \n    return resultado\n\n# Prueba tu función\nprint(contar_ocurrencias(['hola', 'mundo', 'hola', 'python']))",
            'points' => 30,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $pythonExercise3 = ChallengeExercise::create([
            'challenge_id' => $pythonChallenge->id,
            'title' => 'Combinando Estructuras',
            'description' => 'Combina listas y diccionarios para resolver problemas complejos',
            'instructions' => 'Crea una función llamada "agrupar_por_inicial" que reciba una lista de nombres y devuelva un diccionario donde las claves sean las letras iniciales y los valores sean listas con los nombres que empiezan por esa letra. Puedes acceder al primer carácter de un string con nombre[0].',
            'order' => 3,
            'initial_code' => "def agrupar_por_inicial(nombres):\n    # Tu código aquí\n    resultado = {}\n    \n    return resultado\n\n# Prueba tu función\nprint(agrupar_por_inicial(['Ana', 'Alberto', 'Berta', 'Carlos', 'Cristina']))",
            'points' => 40,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Crear un desafío de Prompts IA
        $promptChallenge = TeachingChallenge::create([
            'title' => 'Prompts Efectivos para Generación de Texto',
            'description' => 'Aprende a crear prompts efectivos para modelos de lenguaje como ChatGPT y Claude.',
            'objectives' => 'Entender los principios de diseño de prompts. Aprender técnicas para mejorar resultados. Crear prompts específicos para diferentes tareas.',
            'instructions' => 'Para cada ejercicio, diseña un prompt que consiga el resultado deseado. Recuerda ser específico en tus instrucciones y proporcionar el contexto necesario.',
            'teacher_id' => $teacher->user_id,
            'class_id' => $classroom->class_id,
            'is_public' => true,
            'status' => 'published',
            'challenge_type' => 'ai_prompt',
            'difficulty' => 'principiante',
            'estimated_time' => 45,
            'points' => 80,
            'order' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Ejercicios para el desafío de Prompts IA
        $promptExercise1 = ChallengeExercise::create([
            'challenge_id' => $promptChallenge->id,
            'title' => 'Prompt para Resumen',
            'description' => 'Aprende a crear prompts para obtener resúmenes concisos',
            'instructions' => 'Diseña un prompt que solicite a la IA resumir un artículo científico en no más de 100 palabras, destacando solo los hallazgos principales y la metodología utilizada. Considera incluir instrucciones específicas sobre el formato del resumen y las partes del artículo que deben priorizarse.',
            'order' => 1,
            'initial_code' => "Quiero que actúes como un asistente científico. Te proporcionaré un artículo de investigación y necesito que lo resumas en no más de 100 palabras. El resumen debe incluir: (1) el objetivo principal del estudio, (2) la metodología utilizada y (3) los hallazgos clave. Ignora información como antecedentes extensos o discusiones teóricas. Mantén un tono académico y objetivo.\n\nArtículo: [Aquí iría el texto del artículo]",
            'points' => 25,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $promptExercise2 = ChallengeExercise::create([
            'challenge_id' => $promptChallenge->id,
            'title' => 'Prompt para Análisis de Texto',
            'description' => 'Aprende a solicitar análisis detallados de textos',
            'instructions' => 'Crea un prompt que pida a la IA analizar un poema identificando sus elementos literarios (metáforas, símiles, aliteraciones, etc.) y explicando su significado. Incluye ejemplos de elementos literarios específicos que quieres que la IA identifique en el poema.',
            'order' => 2,
            'initial_code' => "Quiero que analices el siguiente poema como un experto en literatura. Identifica y explica al menos 5 elementos literarios (como metáforas, símiles, aliteraciones, imágenes sensoriales, personificaciones) y cómo contribuyen al significado del poema. Para cada elemento, proporciona la línea exacta donde aparece, explica qué tipo de elemento es y analiza su efecto en el contexto del poema. Finalmente, ofrece una interpretación general del significado del poema basada en tu análisis.\n\nPoema:\n[Aquí iría el poema]",
            'points' => 25,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $promptExercise3 = ChallengeExercise::create([
            'challenge_id' => $promptChallenge->id,
            'title' => 'Prompt para Resolución de Problemas',
            'description' => 'Aprende a guiar a la IA para resolver problemas paso a paso',
            'instructions' => 'Diseña un prompt que solicite a la IA resolver un problema matemático mostrando todos los pasos del proceso y explicando cada uno de forma didáctica, como si estuviera enseñando a un estudiante. Piensa en instrucciones específicas para que la IA explique los conceptos matemáticos subyacentes de forma accesible.',
            'order' => 3,
            'initial_code' => "Quiero que actúes como un tutor de matemáticas que enseña a un estudiante de secundaria. Resuelve el siguiente problema paso a paso, explicando cada operación y concepto de manera simple y didáctica. Después de cada paso, explica por qué se realizó esa operación específica y cómo contribuye a la solución. Asegúrate de utilizar lenguaje accesible y, cuando introduzcas términos técnicos, defínelos brevemente. Al final, resume la solución y verifica que el resultado sea correcto.\n\nProblema: Resolver la ecuación cuadrática 2x² - 5x - 3 = 0 utilizando la fórmula general.",
            'points' => 30,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3. Crear progreso para los estudiantes en estos desafíos
        // Progreso de estudiante 1 en Python (completado)
        $progress1Python = ChallengeStudentProgress::create([
            'challenge_id' => $pythonChallenge->id,
            'student_id' => $student1->user_id,
            'status' => 'completed',
            'total_exercises' => 3,
            'completed_exercises' => 3,
            'score' => 95,
            'started_at' => Carbon::now()->subDays(5),
            'completed_at' => Carbon::now()->subDays(3),
            'last_activity_at' => Carbon::now()->subDays(3),
        ]);

        // Ejercicio 1: Solución correcta
        $submission1 = ExerciseSubmission::create([
            'exercise_id' => $pythonExercise1->id,
            'student_id' => $student1->user_id,
            'submitted_code' => "def procesar_lista(numeros):\n    resultado = []\n    for num in numeros:\n        if num % 2 == 0:  # Es par\n            resultado.append(num * 2)\n        else:  # Es impar\n            resultado.append(num * 3)\n    return resultado\n\n# Prueba tu función\nprint(procesar_lista([1, 2, 3, 4, 5]))",
            'status' => 'graded',
            'score' => 95,
            'feedback' => 'Excelente trabajo. La solución es correcta y eficiente. Has identificado correctamente cómo determinar si un número es par o impar y has aplicado las operaciones adecuadas.',
            'attempt_number' => 1,
            'created_at' => Carbon::now()->subDays(5),
            'updated_at' => Carbon::now()->subDays(5),
        ]);

        // Ejercicio 2: Solución correcta
        $submission2 = ExerciseSubmission::create([
            'exercise_id' => $pythonExercise2->id,
            'student_id' => $student1->user_id,
            'submitted_code' => "def contar_ocurrencias(palabras):\n    resultado = {}\n    for palabra in palabras:\n        if palabra in resultado:\n            resultado[palabra] += 1\n        else:\n            resultado[palabra] = 1\n    return resultado\n\n# Prueba tu función\nprint(contar_ocurrencias(['hola', 'mundo', 'hola', 'python']))",
            'status' => 'graded',
            'score' => 90,
            'feedback' => 'Muy buen trabajo. Tu solución funciona correctamente. Para una versión aún más concisa, podrías usar el método .get() como se sugería en la pista.',
            'attempt_number' => 1,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ]);

        // Ejercicio 3: Solución correcta
        $submission3 = ExerciseSubmission::create([
            'exercise_id' => $pythonExercise3->id,
            'student_id' => $student1->user_id,
            'submitted_code' => "def agrupar_por_inicial(nombres):\n    resultado = {}\n    for nombre in nombres:\n        inicial = nombre[0].upper()\n        if inicial in resultado:\n            resultado[inicial].append(nombre)\n        else:\n            resultado[inicial] = [nombre]\n    return resultado\n\n# Prueba tu función\nprint(agrupar_por_inicial(['Ana', 'Alberto', 'Berta', 'Carlos', 'Cristina']))",
            'status' => 'graded',
            'score' => 100,
            'feedback' => '¡Perfecto! Tu solución es correcta y elegante. Has manejado correctamente el caso de iniciales repetidas y has creado un código legible y eficiente.',
            'attempt_number' => 1,
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(3),
        ]);

        // Progreso de estudiante 2 en ambos desafíos (en progreso)
        $progress2Python = ChallengeStudentProgress::create([
            'challenge_id' => $pythonChallenge->id,
            'student_id' => $student2->user_id,
            'status' => 'in_progress',
            'total_exercises' => 3,
            'completed_exercises' => 1,
            'score' => 85,
            'started_at' => Carbon::now()->subDays(2),
            'completed_at' => null,
            'last_activity_at' => Carbon::now()->subDays(2),
        ]);

        // Sólo ha completado el primer ejercicio
        $submission4 = ExerciseSubmission::create([
            'exercise_id' => $pythonExercise1->id,
            'student_id' => $student2->user_id,
            'submitted_code' => "def procesar_lista(numeros):\n    resultado = []\n    for num in numeros:\n        if num % 2 == 0:  # Es par\n            resultado.append(num * 2)\n        else:  # Es impar\n            resultado.append(num * 3)\n    return resultado\n\nprint(procesar_lista([1, 2, 3, 4, 5]))",
            'status' => 'graded',
            'score' => 85,
            'feedback' => 'Buen trabajo. La solución funciona correctamente. Podrías documentar un poco más tu código con comentarios adicionales para explicar qué hace cada parte.',
            'attempt_number' => 1,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // Progreso en desafío de prompts
        $progress2Prompt = ChallengeStudentProgress::create([
            'challenge_id' => $promptChallenge->id,
            'student_id' => $student2->user_id,
            'status' => 'in_progress',
            'total_exercises' => 3,
            'completed_exercises' => 2,
            'score' => 88,
            'started_at' => Carbon::now()->subDays(1),
            'completed_at' => null,
            'last_activity_at' => Carbon::now()->subHours(12),
        ]);

        // Ha completado los dos primeros ejercicios
        $submission5 = ExerciseSubmission::create([
            'exercise_id' => $promptExercise1->id,
            'student_id' => $student2->user_id,
            'submitted_prompt' => "Actúa como un asistente de investigación científica. Te enviaré un artículo académico y necesito que lo resumas en exactamente 100 palabras o menos. El resumen debe seguir este formato:\n\n1. Objetivo principal del estudio (una frase)\n2. Metodología utilizada (máximo dos frases)\n3. Hallazgos principales (el resto del espacio disponible)\n\nNo incluyas información de fondo, limitaciones del estudio o implicaciones futuras a menos que sean extraordinariamente significativas. Mantén un lenguaje académico y preciso.\n\nArtículo: [texto del artículo aquí]",
            'status' => 'graded',
            'score' => 90,
            'feedback' => 'Excelente prompt. Has estructurado claramente lo que deseas y has especificado tanto el formato como el contenido. La limitación de palabras está clara y has indicado qué priorizar.',
            'attempt_number' => 1,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        $submission6 = ExerciseSubmission::create([
            'exercise_id' => $promptExercise2->id,
            'student_id' => $student2->user_id,
            'submitted_prompt' => "Actúa como un profesor de literatura con especialización en análisis poético. Necesito que analices el siguiente poema línea por línea, identificando y explicando los siguientes elementos literarios:\n\n1. Metáforas y símiles\n2. Aliteraciones y asonancias\n3. Imágenes sensoriales\n4. Personificaciones\n5. Simbolismo\n\nPara cada elemento que identifiques, cita la línea exacta, explica qué tipo de recurso literario es y analiza su significado en el contexto del poema. Concluye con una interpretación general del tema y significado del poema basándote en tu análisis.\n\nPoema: [texto del poema aquí]",
            'status' => 'graded',
            'score' => 85,
            'feedback' => 'Muy buen prompt. Has especificado claramente los elementos literarios a identificar. Para mejorar, podrías incluir también instrucciones sobre cómo deseas que se presente el análisis (por ejemplo, por elemento o por estrofa).',
            'attempt_number' => 1,
            'created_at' => Carbon::now()->subHours(12),
            'updated_at' => Carbon::now()->subHours(12),
        ]);

        // Estudiante 3 no ha iniciado ningún desafío
        $progress3Python = ChallengeStudentProgress::create([
            'challenge_id' => $pythonChallenge->id,
            'student_id' => $student3->user_id,
            'status' => 'not_started',
            'total_exercises' => 3,
            'completed_exercises' => 0,
            'score' => 0,
            'started_at' => null,
            'completed_at' => null,
            'last_activity_at' => null,
        ]);

        $progress3Prompt = ChallengeStudentProgress::create([
            'challenge_id' => $promptChallenge->id,
            'student_id' => $student3->user_id,
            'status' => 'not_started',
            'total_exercises' => 3,
            'completed_exercises' => 0,
            'score' => 0,
            'started_at' => null,
            'completed_at' => null,
            'last_activity_at' => null,
        ]);

        // Mensaje de confirmación
        $this->command->info('Datos de ejemplo creados correctamente:');
        $this->command->info('- 1 profesor (email: ana.garcia' . $timestamp . '@ejemplo.com, password: password123)');
        $this->command->info('- 3 estudiantes:');
        $this->command->info('  - Carlos (email: carlos' . $timestamp . '@ejemplo.com, password: password123)');
        $this->command->info('  - Lucía (email: lucia' . $timestamp . '@ejemplo.com, password: password123)');
        $this->command->info('  - Miguel (email: miguel' . $timestamp . '@ejemplo.com, password: password123)');
        $this->command->info('- 2 desafíos con 3 ejercicios cada uno');
        $this->command->info('- Progreso y entregas para demostrar diferentes estados');
    }
} 