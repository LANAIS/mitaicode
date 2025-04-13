<?php

namespace App\Http\Controllers;

use App\Models\TeachingChallenge;
use App\Models\ChallengeExercise;
use App\Models\ExerciseSubmission;
use App\Models\ChallengeStudentProgress;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ChallengeExerciseController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo ejercicio.
     */
    public function create($challengeId)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::findOrFail($challengeId);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para añadir ejercicios a este desafío.');
        }
        
        return view('challenges.exercises.create', compact('challenge'));
    }

    /**
     * Almacena un nuevo ejercicio en la base de datos.
     */
    public function store(Request $request, $challengeId)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::findOrFail($challengeId);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para añadir ejercicios a este desafío.');
        }
        
        // Validación basada en el tipo de desafío
        $rules = [
            'title' => 'required|string|max:150',
            'instructions' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'points' => 'nullable|integer|min:0',
            'hints' => 'nullable|string',
            'description' => 'nullable|string',
            'difficulty' => 'nullable|string|in:principiante,intermedio,avanzado',
        ];
        
        // Reglas específicas para cada tipo de desafío
        if ($challenge->challenge_type === 'python') {
            $rules['starter_code'] = 'nullable|string';
            $rules['solution_code'] = 'required|string';
            $rules['test_cases'] = 'required|string';
        } elseif ($challenge->challenge_type === 'ai_prompt') {
            $rules['example_prompt'] = 'required|string';
        }
        
        $validated = $request->validate($rules);
        
        // Crear el ejercicio
        $exercise = new ChallengeExercise();
        $exercise->challenge_id = $challenge->id;
        $exercise->title = $validated['title'];
        $exercise->description = $validated['description'] ?? '';
        $exercise->instructions = $validated['instructions'];
        $exercise->hints = $validated['hints'] ?? null;
        
        // Campos específicos según tipo
        if ($challenge->challenge_type === 'python') {
            $exercise->starter_code = $validated['starter_code'] ?? '';
            $exercise->solution_code = $validated['solution_code'];
            
            // Convertir casos de prueba a JSON
            $testCasesText = $validated['test_cases'];
            $testCases = $this->parseTestCases($testCasesText);
            $exercise->test_cases = json_encode($testCases);
        } elseif ($challenge->challenge_type === 'ai_prompt') {
            $exercise->example_prompt = $validated['example_prompt'];
        }
        
        // Orden y puntos
        $exercise->order = $validated['order'] ?? $challenge->exercises()->count();
        $exercise->points = $validated['points'] ?? 10;
        $exercise->difficulty = $validated['difficulty'] ?? 'intermedio';
        
        $exercise->save();
        
        return redirect()->route('challenges.edit', $challenge->id)
            ->with('success', 'Ejercicio añadido con éxito.');
    }

    /**
     * Muestra el formulario para editar un ejercicio.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $exercise = ChallengeExercise::with('challenge')->findOrFail($id);
        $challenge = $exercise->challenge;
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para editar este ejercicio.');
        }
        
        return view('challenges.exercises.edit', compact('exercise', 'challenge'));
    }

    /**
     * Actualiza el ejercicio en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $exercise = ChallengeExercise::with('challenge')->findOrFail($id);
        $challenge = $exercise->challenge;
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para editar este ejercicio.');
        }
        
        // Validación basada en el tipo de desafío
        $rules = [
            'title' => 'required|string|max:150',
            'instructions' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'points' => 'nullable|integer|min:0',
            'hints' => 'nullable|string',
            'description' => 'nullable|string',
            'difficulty' => 'nullable|string|in:principiante,intermedio,avanzado',
        ];
        
        // Reglas específicas para cada tipo de desafío
        if ($challenge->challenge_type === 'python') {
            $rules['starter_code'] = 'nullable|string';
            $rules['solution_code'] = 'required|string';
            $rules['test_cases'] = 'required|string';
        } elseif ($challenge->challenge_type === 'ai_prompt') {
            $rules['example_prompt'] = 'required|string';
        }
        
        $validated = $request->validate($rules);
        
        // Actualizar el ejercicio
        $exercise->title = $validated['title'];
        $exercise->description = $validated['description'] ?? '';
        $exercise->instructions = $validated['instructions'];
        $exercise->hints = $validated['hints'] ?? null;
        
        // Campos específicos según tipo
        if ($challenge->challenge_type === 'python') {
            $exercise->starter_code = $validated['starter_code'] ?? '';
            $exercise->solution_code = $validated['solution_code'];
            
            // Convertir casos de prueba a JSON
            $testCasesText = $validated['test_cases'];
            $testCases = $this->parseTestCases($testCasesText);
            $exercise->test_cases = json_encode($testCases);
        } elseif ($challenge->challenge_type === 'ai_prompt') {
            $exercise->example_prompt = $validated['example_prompt'];
        }
        
        // Orden y puntos
        if (isset($validated['order'])) {
            $exercise->order = $validated['order'];
        }
        
        $exercise->points = $validated['points'] ?? 10;
        $exercise->difficulty = $validated['difficulty'] ?? 'intermedio';
        
        $exercise->save();
        
        return redirect()->route('challenges.edit', $challenge->id)
            ->with('success', 'Ejercicio actualizado con éxito.');
    }

    /**
     * Elimina el ejercicio de la base de datos.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $exercise = ChallengeExercise::with('challenge')->findOrFail($id);
        $challenge = $exercise->challenge;
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para eliminar este ejercicio.');
        }
        
        $exercise->delete();
        
        return redirect()->route('challenges.edit', $challenge->id)
            ->with('success', 'Ejercicio eliminado con éxito.');
    }

    /**
     * Reordena los ejercicios.
     */
    public function reorder(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'exerciseIds' => 'required|array',
            'exerciseIds.*' => 'required|integer|exists:challenge_exercises,id',
        ]);
        
        $exercise = ChallengeExercise::with('challenge')->findOrFail($validated['exerciseIds'][0]);
        $challenge = $exercise->challenge;
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return response()->json(['error' => 'No tienes permiso para reordenar estos ejercicios.'], 403);
        }
        
        // Reordenar los ejercicios
        foreach ($validated['exerciseIds'] as $index => $id) {
            ChallengeExercise::where('id', $id)->update(['order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Prueba un ejercicio (para vista previa).
     */
    public function testExercise(Request $request, $id)
    {
        $user = Auth::user();
        $exercise = ChallengeExercise::with('challenge')->findOrFail($id);
        $challenge = $exercise->challenge;
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return response()->json(['error' => 'No tienes permiso para probar este ejercicio.'], 403);
        }
        
        // Validación basada en el tipo de desafío
        if ($challenge->challenge_type === 'python') {
            $validated = $request->validate([
                'code' => 'required|string',
            ]);
            
            // Aquí iría la lógica para ejecutar el código Python
            // Por ahora simplemente simulamos una respuesta
            
            $testCases = json_decode($exercise->test_cases, true);
            $passedTests = 0;
            $failedTests = [];
            
            foreach ($testCases as $i => $test) {
                // En un entorno real, aquí se ejecutaría el código contra los casos de prueba
                // Simulamos algunos resultados aleatorios para demostración
                if (rand(0, 1) === 1) {
                    $passedTests++;
                } else {
                    $failedTests[] = [
                        'index' => $i,
                        'input' => $test['input'] ?? 'N/A',
                        'expected' => $test['expected'] ?? 'N/A',
                        'actual' => 'Valor simulado incorrecto',
                    ];
                }
            }
            
            $totalTests = count($testCases);
            $score = $totalTests > 0 ? round(($passedTests / $totalTests) * 100) : 0;
            
            return response()->json([
                'success' => true,
                'passed_tests' => $passedTests,
                'total_tests' => $totalTests,
                'failed_tests' => $failedTests,
                'score' => $score,
            ]);
        } elseif ($challenge->challenge_type === 'ai_prompt') {
            $validated = $request->validate([
                'prompt' => 'required|string',
            ]);
            
            // Aquí iría la lógica para evaluar el prompt contra la IA
            // Por ahora simulamos una respuesta
            
            // Simulación de respuesta de la IA
            $aiResponse = "Esta es una respuesta simulada de la IA para el prompt proporcionado.\n\n";
            $aiResponse .= "El prompt parece " . (rand(0, 1) === 1 ? "bueno" : "necesitar mejoras") . ".\n\n";
            $aiResponse .= "Algunas sugerencias:\n";
            $aiResponse .= "- Considere ser más específico sobre lo que necesita\n";
            $aiResponse .= "- Incluya más contexto para obtener mejores resultados\n";
            $aiResponse .= "- Experimente con diferentes enfoques";
            
            return response()->json([
                'success' => true,
                'ai_response' => $aiResponse,
            ]);
        }
        
        return response()->json(['error' => 'Tipo de ejercicio no soportado para pruebas.'], 400);
    }

    /**
     * Parsea los casos de prueba de texto a formato de array.
     */
    private function parseTestCases($testCasesText)
    {
        $cases = [];
        $lines = explode("\n", $testCasesText);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode('>>>', $line, 2);
            if (count($parts) === 2) {
                $input = trim($parts[0]);
                $expected = trim($parts[1]);
                $cases[] = [
                    'input' => $input,
                    'expected' => $expected
                ];
            }
        }
        
        return $cases;
    }
    
    /**
     * Formatea los casos de prueba para mostrarlos en el formulario.
     */
    private function formatTestCases($testCases)
    {
        if (!is_array($testCases)) {
            return '';
        }
        
        $lines = [];
        foreach ($testCases as $case) {
            if (isset($case['input']) && isset($case['expected'])) {
                $lines[] = $case['input'] . ' >>> ' . $case['expected'];
            }
        }
        
        return implode("\n", $lines);
    }

    /**
     * Muestra el formulario para calificar una entrega.
     */
    public function showGradeForm($submissionId)
    {
        $user = Auth::user();
        $submission = ExerciseSubmission::with(['exercise.challenge', 'student'])->findOrFail($submissionId);
        $challenge = $submission->exercise->challenge;
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para calificar esta entrega.');
        }
        
        return view('challenges.exercises.grade', compact('submission'));
    }
    
    /**
     * Procesa la calificación de una entrega.
     */
    public function gradeSubmission(Request $request, $submissionId)
    {
        $user = Auth::user();
        $submission = ExerciseSubmission::with(['exercise.challenge', 'student'])->findOrFail($submissionId);
        $challenge = $submission->exercise->challenge;
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para calificar esta entrega.');
        }
        
        // Validar la calificación
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'feedback' => 'required|string',
        ]);
        
        // Actualizar la entrega
        $submission->score = $validated['score'];
        $submission->feedback = $validated['feedback'];
        $submission->status = 'graded';
        $submission->save();
        
        // Buscar el progreso del estudiante
        $progress = ChallengeStudentProgress::where('challenge_id', $challenge->id)
            ->where('student_id', $submission->student_id)
            ->first();
        
        if ($progress) {
            // Contar ejercicios graded
            $completedCount = ExerciseSubmission::whereIn('exercise_id', $challenge->exercises->pluck('id'))
                ->where('student_id', $submission->student_id)
                ->where('status', 'graded')
                ->select('exercise_id')
                ->distinct()
                ->count();
            
            // Calcular el puntaje promedio
            $avgScore = ExerciseSubmission::whereIn('exercise_id', $challenge->exercises->pluck('id'))
                ->where('student_id', $submission->student_id)
                ->where('status', 'graded')
                ->avg('score') ?? 0;
            
            // Actualizar progreso
            $progress->completed_exercises = $completedCount;
            
            // Si todos los ejercicios están calificados, marcar como completado
            if ($completedCount >= $progress->total_exercises) {
                $progress->status = 'completed';
                $progress->completed_at = now();
                $progress->score = round($avgScore);
                
                // Registrar puntos en el sistema de gamificación
                try {
                    $points = round($avgScore);
                    $gamificationService = app(GamificationService::class);
                    $gamificationService->registerActivity(
                        $submission->student_id,
                        $points,
                        ['completed_challenges' => 1]
                    );
                } catch (\Exception $e) {
                    Log::error('Error al registrar puntos de gamificación: ' . $e->getMessage());
                }
            } else if ($progress->status === 'not_started' && $completedCount > 0) {
                $progress->status = 'in_progress';
                $progress->started_at = now();
                
                // Registrar puntos por ejercicio completado
                try {
                    $points = round($submission->score * 0.1); // 10% de los puntos del ejercicio
                    $gamificationService = app(GamificationService::class);
                    $gamificationService->registerActivity(
                        $submission->student_id,
                        $points,
                        ['completed_exercises' => 1]
                    );
                } catch (\Exception $e) {
                    Log::error('Error al registrar puntos de ejercicio: ' . $e->getMessage());
                }
            }
            
            $progress->last_activity_at = now();
            $progress->save();
        }
        
        return redirect()->route('challenges.exercises.submissions', $submission->exercise_id)
            ->with('success', 'Entrega calificada con éxito.');
    }
    
    /**
     * Muestra las entregas de un ejercicio.
     */
    public function showSubmissions($exerciseId)
    {
        $user = Auth::user();
        $exercise = ChallengeExercise::with('challenge')->findOrFail($exerciseId);
        $challenge = $exercise->challenge;
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para ver las entregas de este ejercicio.');
        }
        
        $submissions = ExerciseSubmission::with('student')
            ->where('exercise_id', $exerciseId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('challenges.exercises.submissions', compact('exercise', 'submissions'));
    }
}
