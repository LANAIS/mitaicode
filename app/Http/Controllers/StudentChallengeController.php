<?php

namespace App\Http\Controllers;

use App\Models\TeachingChallenge;
use App\Models\ChallengeExercise;
use App\Models\ChallengeStudentProgress;
use App\Models\ExerciseSubmission;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GamificationService;

class StudentChallengeController extends Controller
{
    /**
     * Muestra la lista de desafíos disponibles para el estudiante.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar que sea un estudiante
        if ($user->role !== 'student') {
            return redirect()->route('dashboard')
                ->with('error', 'Esta sección es solo para estudiantes.');
        }
        
        // Obtener las clases en las que está inscrito el estudiante
        $enrolledClassIds = DB::table('class_enrollments')
            ->where('student_id', $user->user_id)
            ->where('is_active', true)
            ->pluck('class_id');
        
        // Obtener desafíos de sus clases
        $classDesafios = TeachingChallenge::whereIn('class_id', $enrolledClassIds)
            ->where('status', 'published')
            ->get();
        
        // Obtener desafíos públicos
        $publicDesafios = TeachingChallenge::where('is_public', true)
            ->where('status', 'published')
            ->get();
        
        // Combinar y eliminar duplicados
        $allDesafios = $classDesafios->merge($publicDesafios)->unique('id');
        
        // Obtener progreso del estudiante en estos desafíos
        $progressMap = [];
        foreach ($allDesafios as $desafio) {
            $progress = ChallengeStudentProgress::where('challenge_id', $desafio->id)
                ->where('student_id', $user->user_id)
                ->first();
            
            if (!$progress) {
                // Si no hay registro, crear uno
                $progress = new ChallengeStudentProgress();
                $progress->challenge_id = $desafio->id;
                $progress->student_id = $user->user_id;
                $progress->total_exercises = max(1, $desafio->exercises()->count());
                $progress->completed_exercises = 0;
                $progress->save();
            }
            
            $progressMap[$desafio->id] = $progress;
        }
        
        return view('student.challenges.index', compact('allDesafios', 'progressMap'));
    }
    
    /**
     * Muestra un desafío específico con sus ejercicios.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Verificar que sea un estudiante
        if ($user->role !== 'student') {
            return redirect()->route('dashboard')
                ->with('error', 'Esta sección es solo para estudiantes.');
        }
        
        $challenge = TeachingChallenge::with(['exercises' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($id);
        
        // Verificar si el estudiante tiene acceso a este desafío
        $hasAccess = $this->studentHasAccessToChallenge($user->user_id, $challenge);
        if (!$hasAccess) {
            return redirect()->route('student.challenges.index')
                ->with('error', 'No tienes acceso a este desafío.');
        }
        
        // Obtener o crear registro de progreso
        $progress = ChallengeStudentProgress::firstOrCreate(
            ['challenge_id' => $challenge->id, 'student_id' => $user->user_id],
            [
                'total_exercises' => max(1, $challenge->exercises->count()),
                'completed_exercises' => 0,
                'status' => 'not_started'
            ]
        );
        
        // Si es la primera vez que accede, marcar como iniciado
        if ($progress->status === 'not_started') {
            $progress->markAsStarted();
        }
        
        // Obtener las entregas del estudiante para cada ejercicio
        $submissions = [];
        foreach ($challenge->exercises as $exercise) {
            $submission = ExerciseSubmission::where('exercise_id', $exercise->id)
                ->where('student_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $submissions[$exercise->id] = $submission;
        }
        
        return view('student.challenges.show', compact('challenge', 'progress', 'submissions'));
    }
    
    /**
     * Muestra un ejercicio específico para resolver.
     */
    public function showExercise($exerciseId)
    {
        $user = Auth::user();
        
        // Verificar que sea un estudiante
        if ($user->role !== 'student') {
            return redirect()->route('dashboard')
                ->with('error', 'Esta sección es solo para estudiantes.');
        }
        
        $exercise = ChallengeExercise::with('challenge')->findOrFail($exerciseId);
        $challenge = $exercise->challenge;
        
        // Verificar si el estudiante tiene acceso a este desafío
        $hasAccess = $this->studentHasAccessToChallenge($user->user_id, $challenge);
        if (!$hasAccess) {
            return redirect()->route('student.challenges.index')
                ->with('error', 'No tienes acceso a este ejercicio.');
        }
        
        // Obtener el progreso del estudiante en este desafío
        $progress = ChallengeStudentProgress::firstOrCreate(
            ['challenge_id' => $challenge->id, 'student_id' => $user->user_id],
            [
                'total_exercises' => max(1, $challenge->exercises()->count()),
                'completed_exercises' => 0,
                'status' => 'in_progress'
            ]
        );
        
        // Si es la primera vez que accede, marcar como iniciado
        if ($progress->status === 'not_started') {
            $progress->status = 'in_progress';
            $progress->save();
        }
        
        // Obtener las entregas anteriores del estudiante para este ejercicio
        $submissions = ExerciseSubmission::where('exercise_id', $exercise->id)
            ->where('student_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Obtener la última entrega (si existe)
        $previousSubmission = $submissions->first();
        
        // Calcular el número de intentos
        $attemptNumber = $submissions->count() + 1;
        
        // Obtener una pista si procede
        $hint = null;
        if ($attemptNumber > 1 && $exercise->hint) {
            $hint = $exercise->hint;
        }
        
        return view('student.challenges.exercise', compact(
            'exercise', 
            'challenge', 
            'submissions', 
            'previousSubmission', 
            'attemptNumber', 
            'hint'
        ));
    }
    
    /**
     * Procesa la entrega de un ejercicio.
     */
    public function submit(Request $request, $exerciseId)
    {
        $user = Auth::user();
        
        // Verificar que sea un estudiante
        if ($user->role !== 'student') {
            return redirect()->route('dashboard')
                ->with('error', 'Esta sección es solo para estudiantes.');
        }
        
        $exercise = ChallengeExercise::with('challenge')->findOrFail($exerciseId);
        $challenge = $exercise->challenge;
        
        // Verificar si el estudiante tiene acceso a este desafío
        $hasAccess = $this->studentHasAccessToChallenge($user->user_id, $challenge);
        if (!$hasAccess) {
            return redirect()->route('student.challenges.index')
                ->with('error', 'No tienes acceso a este ejercicio.');
        }
        
        // Validar la entrega
        $challengeType = $challenge->challenge_type;
        if ($challengeType === 'python') {
            $validated = $request->validate([
                'code' => 'required|string',
            ]);
            $submittedContent = $validated['code'];
        } else {
            $validated = $request->validate([
                'prompt' => 'required|string',
            ]);
            $submittedContent = $validated['prompt'];
        }
        
        // Obtener las entregas anteriores del estudiante para este ejercicio
        $submissionCount = ExerciseSubmission::where('exercise_id', $exercise->id)
            ->where('student_id', $user->user_id)
            ->where('status', 'graded')  // Solo contar entregas ya evaluadas
            ->count();
        
        // Crear la nueva entrega
        $submission = new ExerciseSubmission();
        $submission->exercise_id = $exercise->id;
        $submission->student_id = $user->user_id;
        $submission->attempt_number = $submissionCount + 1;
        
        // Evaluar la entrega con OpenAI
        $evaluationResult = $this->evaluateWithAI($exercise, $submittedContent, $challengeType);
        
        if ($challengeType === 'python') {
            $submission->submitted_code = $submittedContent;
            $submission->score = $evaluationResult['score'];
            $submission->feedback = $evaluationResult['feedback'];
            $submission->status = 'graded'; // Evaluado automáticamente por OpenAI
        } else {
            $submission->submitted_prompt = $submittedContent;
            $submission->score = $evaluationResult['score'];
            $submission->feedback = $evaluationResult['feedback'];
            $submission->status = 'graded'; // Evaluado automáticamente por OpenAI
        }
        
        $submission->save();
        
        // Actualizar el progreso del estudiante en el desafío
        $progress = ChallengeStudentProgress::where('challenge_id', $challenge->id)
            ->where('student_id', $user->user_id)
            ->first();
            
        if (!$progress) {
            // Si no existe un registro de progreso, crearlo
            $progress = new ChallengeStudentProgress();
            $progress->challenge_id = $challenge->id;
            $progress->student_id = $user->user_id;
            $progress->total_exercises = $challenge->exercises()->count();
            $progress->status = 'in_progress';
            $progress->score = 0;
        }
        
        // Si es el primer intento aprobado para este ejercicio, incrementar la cuenta de completados
        $isFirstSuccessfulAttempt = $submissionCount === 0 && $evaluationResult['passed'];
        
        // Verificar si ya hay un intento exitoso anterior
        $previousSuccessfulAttempt = false;
        if (!$isFirstSuccessfulAttempt && $evaluationResult['passed']) {
            $previousSuccessfulAttempt = ExerciseSubmission::where('exercise_id', $exercise->id)
                ->where('student_id', $user->user_id)
                ->where('status', 'graded')
                ->where(function($query) {
                    $query->where('score', '>=', 60)
                          ->orWhere('feedback', 'like', '%aprobado%');
                })
                ->exists();
                
            // Si no hay un intento exitoso anterior, este es el primero
            if (!$previousSuccessfulAttempt) {
                $isFirstSuccessfulAttempt = true;
                Log::info("No se encontraron intentos exitosos anteriores, marcando este como el primero.");
            }
        }
        
        // IMPORTANTE: Si la puntuación es alta (>=80), considerarlo como un intento exitoso sin importar otros factores
        if ($evaluationResult['score'] >= 80 && !$isFirstSuccessfulAttempt) {
            // Verificar si este ejercicio ya ha sido marcado como completado
            $isAlreadyCompleted = false;
            
            // Contar cuántos ejercicios realmente completados hay (con entregas exitosas)
            $completedExercisesCount = ExerciseSubmission::where('student_id', $user->user_id)
                ->where('status', 'graded')
                ->where('score', '>=', 60)
                ->select('exercise_id')
                ->distinct()
                ->count();
            
            // Si el progreso muestra menos ejercicios completados que los realmente completados,
            // entonces es probable que este ejercicio no esté contado
            if ($progress->completed_exercises < $completedExercisesCount) {
                $isFirstSuccessfulAttempt = true;
                Log::info("Forzando ejercicio como completado debido a puntuación alta ({$evaluationResult['score']}) y discrepancia en conteo.");
            }
        }
        
        if ($isFirstSuccessfulAttempt) {
            // Verificar que este ejercicio no ya ha sido marcado como completado
            $alreadyCompleted = DB::table('exercise_completion_records')
                ->where('student_id', $user->user_id)
                ->where('exercise_id', $exercise->id)
                ->exists();
                
            if (!$alreadyCompleted) {
                // Registrar que este ejercicio ha sido completado
                DB::table('exercise_completion_records')->insert([
                    'student_id' => $user->user_id,
                    'exercise_id' => $exercise->id,
                    'submission_id' => $submission->id,
                    'completed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Incrementar contador de ejercicios completados
                $progress->completed_exercises += 1;
                Log::info("EJERCICIO MARCADO COMO COMPLETADO: Usuario {$user->user_id}, Ejercicio ID: {$exercise->id}");
            } else {
                Log::info("El ejercicio ya estaba marcado como completado anteriormente");
            }
            
            try {
                // Registrar la actividad en el sistema de gamificación
                $gamification = new GamificationService();
                
                // Calcular puntos basados en la puntuación (hasta un máximo de 100 puntos por ejercicio)
                $pointsEarned = min(100, max(0, (int)$evaluationResult['score']));
                
                // Registrar la actividad directamente con los valores correctos
                Log::info("Registrando actividad para usuario {$user->user_id} - Puntos: {$pointsEarned}");
                $gamification->registerActivity(
                    $user->user_id, 
                    $pointsEarned, 
                    ['completed_exercises' => 1]
                );
                
                // Si completó todos los ejercicios, marcar el desafío como completado
                if ($progress->completed_exercises >= $progress->total_exercises) {
                    // Verificar si no estaba previamente completado (para evitar duplicar eventos)
                    $wasAlreadyCompleted = $progress->status === 'completed';
                    
                    // Marcar como completado
                    $progress->status = 'completed';
                    $progress->completed_at = now();
                    
                    // Registrar el desafío completado en gamificación
                    Log::info("Usuario {$user->user_id} completó todo el desafío. Otorgando 200 puntos bonus");
                    $gamification->registerActivity(
                        $user->user_id, 
                        200, // Bonificación por completar todo el desafío
                        ['completed_challenges' => 1]
                    );
                    
                    // Establecer variable de sesión para mostrar modal de celebración
                    // pero solo si el desafío no estaba ya completado previamente
                    if (!$wasAlreadyCompleted) {
                        session()->flash('challenge_just_completed', true);
                        Log::info("Preparando celebración para usuario {$user->user_id} - Desafío {$challenge->id}");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error al registrar puntos de gamificación: " . $e->getMessage());
                // Seguimos con la ejecución aunque falle la gamificación
            }
        }
        
        // Actualizar puntuación promedio
        $progress->score = ExerciseSubmission::where('student_id', $user->user_id)
            ->whereIn('exercise_id', $challenge->exercises()->pluck('id'))
            ->avg('score') ?? 0;
        
        $progress->last_activity_at = now();
        $progress->save();
        
        // Verificar si el desafío está completado después de este envío
        $challengeCompleted = ($progress->status === 'completed');
        
        // Redireccionar según corresponda
        if ($challengeCompleted && session('challenge_just_completed')) {
            // Si se acaba de completar el desafío, ir a la página del desafío para mostrar celebración
            return redirect()->route('student.challenges.show', [
                'id' => $challenge->id
            ])->with('success', '¡Felicidades! Has completado el desafío exitosamente.');
        } else {
            // Redireccionar a la página de resultados
            return redirect()->route('student.challenges.exercise.result', [
                'submissionId' => $submission->id
            ])->with('success', 'Tu ejercicio ha sido evaluado exitosamente.');
        }
    }
    
    /**
     * Muestra los detalles de una entrega.
     */
    public function showSubmission($submissionId)
    {
        $user = Auth::user();
        
        // Verificar que sea un estudiante
        if ($user->role !== 'student') {
            return redirect()->route('dashboard')
                ->with('error', 'Esta sección es solo para estudiantes.');
        }
        
        $submission = ExerciseSubmission::with(['exercise.challenge'])->findOrFail($submissionId);
        
        // Verificar que la entrega pertenezca al estudiante
        if ($submission->student_id !== $user->user_id) {
            return redirect()->route('student.challenges.index')
                ->with('error', 'No tienes acceso a esta entrega.');
        }
        
        // Contar el número de intentos total para este ejercicio
        $submissionNumber = ExerciseSubmission::where('exercise_id', $submission->exercise_id)
            ->where('student_id', $user->user_id)
            ->where('created_at', '<=', $submission->created_at)
            ->count();
        
        return view('student.challenges.submission', compact('submission', 'submissionNumber'));
    }
    
    /**
     * Verifica si un estudiante tiene acceso a un desafío.
     */
    private function studentHasAccessToChallenge($studentId, $challenge)
    {
        // Si el desafío es público, tiene acceso
        if ($challenge->is_public) {
            return true;
        }
        
        // Si no es público, verificar si el estudiante está inscrito en la clase
        if ($challenge->class_id) {
            $isEnrolled = DB::table('class_enrollments')
                ->where('class_id', $challenge->class_id)
                ->where('student_id', $studentId)
                ->where('is_active', true)
                ->exists();
            
            return $isEnrolled;
        }
        
        return false;
    }
    
    /**
     * Utiliza OpenAI para evaluar automáticamente un ejercicio.
     * 
     * @param ChallengeExercise $exercise El ejercicio a evaluar
     * @param string $submittedContent El contenido enviado por el estudiante
     * @param string $type El tipo de evaluación (prompt, código, etc.)
     * @return array Resultado de la evaluación
     */
    private function evaluateWithAI($exercise, $submittedContent, $type = 'prompt')
    {
        // Establecer la puntuación y mensaje predeterminados
        $result = [
            'score' => 0,
            'feedback' => 'No se pudo evaluar el ejercicio automáticamente.',
            'passed' => false
        ];
        
        try {
            // Registrar información sobre la evaluación
            Log::info("------------------------ NUEVA EVALUACIÓN ------------------------");
            Log::info("Iniciando evaluación con OpenAI para ejercicio ID: {$exercise->id}, tipo: {$type}");
            
            // Usar el servicio de OpenAI de Laravel
            $openai = app(\App\Services\OpenAIService::class);
            
            // Log para verificar que se cargó el servicio
            Log::info("Servicio OpenAI cargado correctamente");
            
            // Preparar el prompt para OpenAI según el tipo de ejercicio
            $prompt = $this->prepareEvaluationPrompt($exercise, $submittedContent, $type);
            
            // Log del prompt (parte inicial para no sobrecargar los logs)
            Log::info("Prompt preparado para OpenAI: " . substr($prompt, 0, 200) . "...");
            
            // Realizar la llamada a OpenAI según el tipo de ejercicio
            if ($type === 'code' || $type === 'python') {
                // Usar el método de análisis de código
                Log::info("Llamando a openai->analyzeCode()");
                $response = $openai->analyzeCode($submittedContent, $prompt);
                Log::info("Llamada a analyzeCode completada");
            } else {
                // Usar el método estándar para prompts y otros tipos
                Log::info("Llamando a openai->generateText() con modelo gpt-3.5-turbo");
                $response = $openai->generateText($prompt, [
                    'model' => 'gpt-3.5-turbo', // Usar GPT-3.5 que es más rápido y económico
                    'temperature' => 0.3,        // Temperatura baja para respuestas más deterministas
                    'max_tokens' => 1000
                ]);
                Log::info("Llamada a generateText completada");
            }
            
            // Registrar estructura de la respuesta para análisis
            Log::info("Estructura de respuesta de OpenAI: " . json_encode(array_keys($response)));
            
            // Verificar si hay error
            if (isset($response['error']) && $response['error']) {
                Log::error("Error en respuesta de OpenAI: " . json_encode($response));
            } else {
                Log::info("Respuesta recibida de OpenAI sin errores reportados");
            }
            
            // Verificar si hay contenido
            if (isset($response['content'])) {
                Log::info("Contenido encontrado en la respuesta, longitud: " . strlen($response['content']));
            } else {
                Log::error("No se encontró 'content' en la respuesta de OpenAI");
                Log::error("Respuesta completa: " . json_encode($response));
            }
            
            // Procesar la respuesta para extraer la puntuación y retroalimentación
            if (isset($response['content']) && !empty($response['content'])) {
                // Procesar respuesta normal de OpenAI
                Log::info("Procesando respuesta de OpenAI: " . substr($response['content'], 0, 200) . "...");
                
                $result = $this->parseAIResponse($response['content']);
                
                // Añadir feedback adicional según la puntuación
                $result['feedback'] = $this->enhanceFeedback($result['feedback'], $result['score'], $type);
                
                // Registrar el resultado final
                Log::info("Evaluación del ejercicio ID {$exercise->id}: Puntuación={$result['score']}, Aprobado=" . 
                    ($result['passed'] ? 'Sí' : 'No'));
                
            } else if (isset($response['error']) && $response['error']) {
                // Si hay un error específico, registrarlo y dar una calificación razonable
                Log::warning("Evaluación fallida. Usando calificación por defecto. Motivo: " . ($response['message'] ?? 'Desconocido'));
                
                // Proporcionar una calificación por defecto con feedback genérico
                $result = $this->getDefaultEvaluation($exercise, $submittedContent, $type);
                Log::warning("Usando evaluación alternativa debido a un error reportado");
            } else {
                // Si no hay contenido ni error explícito, algo raro pasó
                Log::error("Respuesta inesperada de OpenAI: " . json_encode($response));
                $result = $this->getDefaultEvaluation($exercise, $submittedContent, $type);
                Log::warning("Usando evaluación alternativa debido a una respuesta inesperada");
            }
        } catch (\Exception $e) {
            // Registrar la excepción completa
            Log::error('Excepción al evaluar con OpenAI: ' . $e->getMessage());
            Log::error('Detalles: ' . $e->getTraceAsString());
            
            // En caso de error, establecer una calificación razonable con feedback explicativo
            $result = $this->getDefaultEvaluation($exercise, $submittedContent, $type);
            Log::warning("Usando evaluación alternativa debido a una excepción");
        }
        
        // Log final resumen
        if ($result['feedback'] === 'No se pudo evaluar el ejercicio automáticamente.' || 
            strpos($result['feedback'], 'Debido a limitaciones técnicas') !== false) {
            Log::warning("RESULTADO FINAL: Se utilizó el sistema de evaluación alternativa");
        } else {
            Log::info("RESULTADO FINAL: Evaluación con OpenAI exitosa");
        }
        
        return $result;
    }
    
    /**
     * Prepara el prompt para la evaluación basado en el tipo de ejercicio.
     * 
     * @param ChallengeExercise $exercise El ejercicio a evaluar
     * @param string $submittedContent El contenido enviado por el estudiante
     * @param string $type El tipo de evaluación
     * @return string El prompt completo para OpenAI
     */
    private function prepareEvaluationPrompt($exercise, $submittedContent, $type)
    {
        // Obtener los criterios desde el ejercicio si están disponibles
        $criteria = $exercise->evaluation_criteria ?? 'Evaluar la calidad y efectividad de la respuesta.';
        $instructions = $exercise->instructions ?? 'Sin instrucciones específicas.';
        $solution = $exercise->model_solution ?? '';
        
        // Crear prompt base según el tipo
        if ($type === 'prompt') {
            return "Actúa como un evaluador educativo experto en ingeniería de prompts. Evalúa el siguiente prompt creado por un estudiante:\n\n" .
                   "INSTRUCCIONES DEL EJERCICIO:\n{$instructions}\n\n" .
                   "CRITERIOS DE EVALUACIÓN:\n{$criteria}\n\n" .
                   "RESPUESTA DEL ESTUDIANTE:\n{$submittedContent}\n\n" .
                   "SOLUCIÓN MODELO (referencia):\n{$solution}\n\n" .
                   "Por favor, evalúa esta respuesta en una escala de 0 a 100 puntos y proporciona retroalimentación constructiva.\n" .
                   "Tu respuesta debe seguir este formato exacto:\n" .
                   "PUNTUACIÓN: [número del 0 al 100]\n" .
                   "RETROALIMENTACIÓN: [tu análisis detallado]\n" .
                   "APROBADO: [SI/NO] (aprobar si la puntuación es 60 o superior)";
        } 
        elseif ($type === 'code' || $type === 'python') {
            return "Actúa como un evaluador experto en programación. Evalúa el siguiente código Python creado por un estudiante:\n\n" .
                   "INSTRUCCIONES DEL EJERCICIO:\n{$instructions}\n\n" .
                   "CRITERIOS DE EVALUACIÓN:\n{$criteria}\n\n" .
                   "CÓDIGO DEL ESTUDIANTE:\n```python\n{$submittedContent}\n```\n\n" .
                   "SOLUCIÓN MODELO (referencia):\n```python\n{$solution}\n```\n\n" .
                   "Por favor, evalúa este código en una escala de 0 a 100 puntos y proporciona retroalimentación constructiva.\n" .
                   "Tu respuesta debe seguir este formato exacto:\n" .
                   "PUNTUACIÓN: [número del 0 al 100]\n" .
                   "RETROALIMENTACIÓN: [tu análisis detallado]\n" .
                   "APROBADO: [SI/NO] (aprobar si la puntuación es 60 o superior)";
        }
        else {
            return "Actúa como un evaluador educativo experto. Evalúa la siguiente respuesta creada por un estudiante:\n\n" .
                   "INSTRUCCIONES DEL EJERCICIO:\n{$instructions}\n\n" .
                   "CRITERIOS DE EVALUACIÓN:\n{$criteria}\n\n" .
                   "RESPUESTA DEL ESTUDIANTE:\n{$submittedContent}\n\n" .
                   "SOLUCIÓN MODELO (referencia):\n{$solution}\n\n" .
                   "Por favor, evalúa esta respuesta en una escala de 0 a 100 puntos y proporciona retroalimentación constructiva.\n" .
                   "Tu respuesta debe seguir este formato exacto:\n" .
                   "PUNTUACIÓN: [número del 0 al 100]\n" .
                   "RETROALIMENTACIÓN: [tu análisis detallado]\n" .
                   "APROBADO: [SI/NO] (aprobar si la puntuación es 60 o superior)";
        }
    }
    
    /**
     * Analiza la respuesta de OpenAI para extraer la puntuación y retroalimentación.
     * 
     * @param string $response La respuesta de la API
     * @return array Los resultados procesados
     */
    private function parseAIResponse($response)
    {
        $result = [
            'score' => 0,
            'feedback' => 'No se pudo procesar la respuesta.',
            'passed' => false
        ];
        
        try {
            // Limpiar la respuesta (eliminar posibles caracteres de control o unicode inválidos)
            $response = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $response);
            
            // Log para debugging
            Log::debug("Respuesta a parsear: " . substr($response, 0, 200) . "...");
            
            // Patrones más robustos para extraer la puntuación
            $scorePatterns = [
                '/PUNTUACIÓN:\s*(\d+)/i',
                '/PUNTUACION:\s*(\d+)/i',
                '/SCORE:\s*(\d+)/i',
                '/PUNTAJE:\s*(\d+)/i',
                '/PUNTACION:\s*(\d+)/i',
                '/PUNTOS:\s*(\d+)/i',
                '/(\d+)\s*\/\s*100/i',
                '/(\d+)\s*de\s*100/i',
                '/(\d+)\s*puntos/i',
                '/La puntuación es:?\s*(\d+)/i',
                '/calificación\s*(?:es|:)?\s*(\d+)/i',
            ];
            
            // Intentar extraer la puntuación con los patrones
            $scoreFound = false;
            foreach ($scorePatterns as $pattern) {
                if (preg_match($pattern, $response, $scoreMatches)) {
                    $score = intval($scoreMatches[1]);
                    // Verificar que el score está en un rango válido
                    if ($score >= 0 && $score <= 100) {
                        $result['score'] = $score;
                        $scoreFound = true;
                        Log::info("Puntuación encontrada con patrón {$pattern}: {$score}");
                        break;
                    }
                }
            }
            
            // Si no encontramos la puntuación con los patrones principales
            if (!$scoreFound) {
                // Buscar cualquier número que parezca una puntuación
                preg_match_all('/\b(\d{1,3})\b/', $response, $possibleScores);
                if (!empty($possibleScores[1])) {
                    foreach ($possibleScores[1] as $possibleScore) {
                        $intScore = intval($possibleScore);
                        if ($intScore >= 0 && $intScore <= 100) {
                            $result['score'] = $intScore;
                            Log::info("Puntuación inferida de números en el texto: {$intScore}");
                            break; // Usar el primer número válido encontrado
                        }
                    }
                }
                
                // Si todavía no tenemos puntuación, usar análisis de sentimiento
                if ($result['score'] == 0) {
                    Log::warning("No se pudo encontrar una puntuación numérica, usando análisis de sentimiento");
                    // Análisis de sentimiento simple
                    $positiveWords = ['excelente', 'bueno', 'correcto', 'bien', 'genial', 'perfecto'];
                    $negativeWords = ['error', 'incorrecto', 'mal', 'problema', 'falla', 'mejorar'];
                    
                    $positiveCount = 0;
                    $negativeCount = 0;
                    
                    foreach ($positiveWords as $word) {
                        $positiveCount += substr_count(strtolower($response), $word);
                    }
                    
                    foreach ($negativeWords as $word) {
                        $negativeCount += substr_count(strtolower($response), $word);
                    }
                    
                    // Calcular puntuación basada en palabras positivas vs negativas
                    if ($positiveCount + $negativeCount > 0) {
                        $ratio = $positiveCount / ($positiveCount + $negativeCount);
                        $result['score'] = min(85, max(40, intval($ratio * 100)));
                        Log::info("Puntuación basada en sentimiento: {$result['score']} (positivas: {$positiveCount}, negativas: {$negativeCount})");
                    } else {
                        $result['score'] = 65; // Valor por defecto si no hay información
                        Log::info("Usando puntuación por defecto: 65");
                    }
                }
            }
            
            // Extraer retroalimentación con patrones robustos
            $feedbackPatterns = [
                '/RETROALIMENTACIÓN:(.*?)(?:APROBADO:|$)/is',
                '/RETROALIMENTACION:(.*?)(?:APROBADO:|$)/is',
                '/FEEDBACK:(.*?)(?:APROBADO:|PASSED:|$)/is',
                '/COMENTARIOS:(.*?)(?:APROBADO:|$)/is',
            ];
            
            $feedbackFound = false;
            foreach ($feedbackPatterns as $pattern) {
                if (preg_match($pattern, $response, $feedbackMatches)) {
                    $result['feedback'] = trim($feedbackMatches[1]);
                    $feedbackFound = true;
                    break;
                }
            }
            
            if (!$feedbackFound) {
                // Si no encontramos un patrón específico, usar toda la respuesta como feedback
                // pero quitar cualquier referencia a PUNTUACIÓN que pueda haber
                $result['feedback'] = preg_replace('/PUNTUACI[OÓ]N:?\s*\d+\s*/i', '', $response);
                $result['feedback'] = trim($result['feedback']);
            }
            
            // Extraer si ha aprobado con patrones robustos
            $passPatterns = [
                '/APROBADO:\s*(S[IÍ]|YES|VERDADERO|TRUE|PASS|NO|FALSE|FALSO|FAIL)/i',
                '/PASSED:\s*(YES|TRUE|NO|FALSE)/i',
                '/APROB(Ó|O|ADO):\s*(S[IÍ]|YES|VERDADERO|TRUE|PASS|NO|FALSE|FALSO|FAIL)/i',
            ];
            
            $passedFound = false;
            foreach ($passPatterns as $pattern) {
                if (preg_match($pattern, $response, $passedMatches)) {
                    $passResult = strtoupper($passedMatches[1]);
                    $result['passed'] = in_array($passResult, ['SÍ', 'SI', 'YES', 'VERDADERO', 'TRUE', 'PASS']);
                    $passedFound = true;
                    Log::info("Estado de aprobación encontrado: " . ($result['passed'] ? 'Aprobado' : 'No aprobado'));
                    break;
                }
            }
            
            if (!$passedFound) {
                // Si no hay resultado explícito, aprobar si la puntuación es >= 60
                $result['passed'] = ($result['score'] >= 60);
                Log::info("Estado de aprobación inferido desde puntuación: " . ($result['passed'] ? 'Aprobado' : 'No aprobado'));
            }
            
            // FORZAR APROBADO para puntuaciones altas (>= 80)
            if ($result['score'] >= 80 && !$result['passed']) {
                $result['passed'] = true;
                Log::info("Forzando aprobación debido a puntuación alta ({$result['score']})");
            }
            
            // Si no hay feedback o es muy corto, generar uno básico
            if (strlen($result['feedback']) < 20) {
                if ($result['score'] >= 80) {
                    $result['feedback'] = "Tu respuesta es excelente y cumple con todos los criterios de evaluación.";
                } elseif ($result['score'] >= 60) {
                    $result['feedback'] = "Tu respuesta cumple con los criterios básicos, pero hay aspectos que podrías mejorar.";
                } else {
                    $result['feedback'] = "Tu respuesta necesita mejoras significativas. Revisa cuidadosamente los requisitos del ejercicio.";
                }
                Log::info("Feedback generado automáticamente debido a feedback corto o ausente");
            }
            
        } catch (\Exception $e) {
            Log::error("Error al parsear respuesta de OpenAI: " . $e->getMessage());
            // En caso de error, dar un resultado razonable
            $result['score'] = 65;
            $result['feedback'] = "Debido a un error técnico, no pudimos procesar la respuesta completa. Tu solución ha sido calificada con {$result['score']} puntos.";
            $result['passed'] = true;
        }
        
        // Registrar el resultado del parsing
        Log::info("Resultado de parsing: Puntuación={$result['score']}, Aprobado=" . ($result['passed'] ? 'Sí' : 'No'));
        
        return $result;
    }

    /**
     * Muestra los resultados de la evaluación automática realizada por OpenAI.
     */
    public function showResult($submissionId)
    {
        $user = Auth::user();
        
        // Verificar que sea un estudiante
        if ($user->role !== 'student') {
            return redirect()->route('dashboard')
                ->with('error', 'Esta sección es solo para estudiantes.');
        }
        
        // Obtener la entrega
        $submission = ExerciseSubmission::with(['exercise.challenge'])->findOrFail($submissionId);
        
        // Verificar que la entrega pertenece al estudiante
        if ($submission->student_id !== $user->user_id) {
            return redirect()->route('student.challenges.index')
                ->with('error', 'No tienes acceso a esta entrega.');
        }
        
        $exercise = $submission->exercise;
        $challenge = $exercise->challenge;
        
        // Obtener el progreso del estudiante en este desafío
        $progress = ChallengeStudentProgress::where('challenge_id', $challenge->id)
            ->where('student_id', $user->user_id)
            ->first();
        
        return view('student.challenges.result', compact('submission', 'exercise', 'challenge', 'progress'));
    }

    /**
     * Genera una evaluación por defecto cuando OpenAI falla.
     * Analiza básicamente el contenido para dar una calificación aproximada.
     * 
     * @param ChallengeExercise $exercise
     * @param string $submittedContent
     * @param string $type
     * @return array
     */
    private function getDefaultEvaluation($exercise, $submittedContent, $type)
    {
        // Análisis básico del contenido para dar una puntuación aproximada
        $length = strlen($submittedContent);
        $wordCount = str_word_count($submittedContent);
        
        // Criterios simples para una evaluación básica
        $score = 65; // Puntuación base de 65
        
        // Ajustar según la longitud y complejidad
        if ($length < 50) {
            $score = max(50, $score - 15); // Penalizar respuestas muy cortas
        } elseif ($length > 500) {
            $score = min(75, $score + 10); // Bonificar respuestas extensas
        }
        
        if ($wordCount < 10) {
            $score = max(45, $score - 20); // Penalizar respuestas con muy pocas palabras
        }
        
        // Ajustar según si contiene palabras clave del ejercicio
        $keywords = explode(' ', $exercise->title . ' ' . $exercise->description);
        $keywordCount = 0;
        
        foreach ($keywords as $keyword) {
            if (strlen($keyword) > 4 && stripos($submittedContent, $keyword) !== false) {
                $keywordCount++;
            }
        }
        
        if ($keywordCount > 3) {
            $score = min(85, $score + 10); // Bonificar uso de palabras clave relevantes
        }
        
        // Determinar si pasa
        $passed = ($score >= 60);
        
        // Generar feedback básico
        $feedback = "Debido a limitaciones técnicas, tu ejercicio ha sido evaluado usando un sistema alternativo.\n\n";
        
        if ($score >= 80) {
            $feedback .= "Tu respuesta parece ser completa y detallada. Has abordado los puntos principales del ejercicio de manera efectiva. ¡Felicidades por tu excelente trabajo!";
        } elseif ($score >= 70) {
            $feedback .= "Tu respuesta es buena y cubre aspectos importantes del ejercicio. Hay algunos detalles que podrías mejorar, pero en general has hecho un buen trabajo.";
        } elseif ($score >= 60) {
            $feedback .= "Tu respuesta cumple con los requisitos mínimos del ejercicio. Es importante que revises el contenido para reforzar tu comprensión del tema.";
        } else {
            $feedback .= "Tu respuesta necesita más desarrollo. Te recomendamos revisar las instrucciones del ejercicio y volver a intentarlo con más detalle.";
        }
        
        return [
            'score' => $score,
            'feedback' => $feedback,
            'passed' => $passed
        ];
    }
    
    /**
     * Mejora el feedback añadiendo comentarios adicionales según la puntuación.
     * 
     * @param string $originalFeedback El feedback original
     * @param int $score La puntuación
     * @param string $type El tipo de ejercicio
     * @return string Feedback mejorado
     */
    private function enhanceFeedback($originalFeedback, $score, $type)
    {
        $additionalFeedback = "\n\n";
        
        if ($score >= 90) {
            $additionalFeedback .= "🏆 ¡Excelente trabajo! Tu solución es sobresaliente y demuestra un gran dominio del tema. Sigue así.";
        } elseif ($score >= 80) {
            $additionalFeedback .= "👏 ¡Muy buen trabajo! Tu solución demuestra una buena comprensión del tema, con sólo algunos detalles por mejorar.";
        } elseif ($score >= 70) {
            $additionalFeedback .= "👍 Buen trabajo. Tu solución es correcta en general, aunque hay áreas que podrías mejorar para alcanzar una comprensión más profunda.";
        } elseif ($score >= 60) {
            $additionalFeedback .= "✅ Has aprobado el ejercicio, pero hay aspectos importantes que debes revisar. Intenta mejorar tu solución siguiendo el feedback proporcionado.";
        } else {
            $additionalFeedback .= "❌ Tu solución necesita más trabajo. Te recomendamos revisar el material de estudio y volver a intentarlo. ¡No te desanimes, es parte del proceso de aprendizaje!";
        }
        
        // Añadir consejos específicos según el tipo de ejercicio
        if ($type === 'python' || $type === 'code') {
            if ($score < 60) {
                $additionalFeedback .= "\n\nRecuerda revisar la sintaxis, la lógica del programa y asegurarte de que tu solución cumple con todos los requisitos del ejercicio.";
            }
        } else { // prompt
            if ($score < 60) {
                $additionalFeedback .= "\n\nRecuerda que un buen prompt debe ser claro, específico y proporcionar el contexto necesario para obtener el resultado deseado.";
            }
        }
        
        return $originalFeedback . $additionalFeedback;
    }
}
