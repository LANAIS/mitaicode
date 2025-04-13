<?php

namespace App\Http\Controllers;

use App\Models\PromptExercise;
use App\Models\PromptLesson;
use App\Models\PromptLessonProgress;
use App\Models\PromptSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PromptExerciseController extends Controller
{
    /**
     * Muestra un ejercicio específico.
     */
    public function show($exerciseId)
    {
        $exercise = PromptExercise::with(['lesson.teacher', 'lesson.classroom'])->findOrFail($exerciseId);
        $lesson = $exercise->lesson;
        
        $user = Auth::user();
        $canAccess = false;
        
        if ($user->role === 'admin' || $user->user_id === $lesson->teacher_id) {
            $canAccess = true;
        } elseif ($lesson->is_public) {
            $canAccess = true;
        } elseif ($lesson->class_id && $user->studentEnrollments->contains('class_id', $lesson->class_id)) {
            $canAccess = true;
        }
        
        if (!$canAccess) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes acceso a este ejercicio.');
        }
        
        // Verificar si el estudiante ya ha enviado este ejercicio
        $submission = null;
        if ($user->role === 'student') {
            $submission = PromptSubmission::where('exercise_id', $exerciseId)
                ->where('student_id', $user->user_id)
                ->orderBy('attempt_number', 'desc')
                ->first();
        }
        
        return view('prompt_exercises.show', compact('exercise', 'lesson', 'submission'));
    }
    
    /**
     * Procesa la prueba de un prompt.
     */
    public function testPrompt(Request $request, $exerciseId)
    {
        $request->validate([
            'prompt_text' => 'required|string|min:5|max:2000'
        ]);
        
        $exercise = PromptExercise::findOrFail($exerciseId);
        
        try {
            // Aquí iría la lógica para enviar el prompt a la API de OpenAI
            // y obtener la respuesta. Esto es un simulador simple.
            
            // Simulación de respuesta de IA
            $aiResponse = $this->simulateAIResponse($request->prompt_text, $exercise);
            
            return response()->json([
                'success' => true,
                'ai_response' => $aiResponse
            ]);
        } catch (\Exception $e) {
            Log::error('Error al procesar prompt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el prompt. Inténtalo de nuevo.'
            ], 500);
        }
    }
    
    /**
     * Simula una respuesta de IA. En producción, esto se reemplazaría por una llamada a la API de OpenAI.
     */
    private function simulateAIResponse($promptText, $exercise)
    {
        // Determinar tipo de respuesta basado en el ejercicio
        $exerciseTitle = strtolower($exercise->title);
        
        if (strpos($exerciseTitle, 'imagen') !== false || strpos($promptText, 'imagen') !== false) {
            return "<p>Aquí tienes una imagen generada con IA basada en tu descripción.</p>
                <p><i>(En la versión de producción, se mostraría una imagen real generada con DALL-E u otro modelo similar basado en tu prompt)</i></p>";
        } 
        
        elseif (strpos($exerciseTitle, 'explicación') !== false || strpos($promptText, 'explica') !== false) {
            return "<p>Tu prompt solicita una explicación. Aquí hay una respuesta a tu solicitud:</p>
                <p>La ingeniería de prompts es el arte y la ciencia de diseñar instrucciones efectivas para modelos de IA. Un buen prompt es claro, específico y proporciona el contexto necesario para que el modelo entienda exactamente lo que necesitas.</p>
                <p>Los elementos clave incluyen:</p>
                <ul>
                    <li>Ser específico sobre lo que quieres</li>
                    <li>Proporcionar contexto relevante</li>
                    <li>Estructurar la información de manera clara</li>
                    <li>Definir el formato de respuesta deseado</li>
                </ul>";
        }
        
        elseif (strpos($exerciseTitle, 'código') !== false || strpos($promptText, 'código') !== false) {
            return "<p>Basado en tu solicitud de código, he generado este ejemplo:</p>
                <pre><code>
def analizar_prompt(prompt):
    \"\"\"
    Analiza la calidad de un prompt basado en criterios básicos.
    
    Args:
        prompt (str): El texto del prompt a analizar
        
    Returns:
        dict: Diccionario con puntuaciones y recomendaciones
    \"\"\"
    resultados = {
        'claridad': 0,
        'especificidad': 0,
        'contexto': 0,
        'recomendaciones': []
    }
    
    # Analizar claridad (longitud adecuada, no demasiado corto ni largo)
    if 20 <= len(prompt) <= 500:
        resultados['claridad'] = 5
    elif len(prompt) < 20:
        resultados['claridad'] = 2
        resultados['recomendaciones'].append('Prompt demasiado corto, añade más detalles')
    else:
        resultados['claridad'] = 3
        resultados['recomendaciones'].append('Prompt muy largo, considera hacerlo más conciso')
    
    return resultados
                </code></pre>";
        }
        
        else {
            return "<p>He recibido tu prompt y lo he analizado. Aquí está mi respuesta:</p>
                <p>Tu prompt es " . (strlen($promptText) > 100 ? "bastante detallado" : "relativamente breve") . " y " .
                (strpos($promptText, '?') !== false ? "formula una pregunta clara" : "presenta una instrucción") . ".</p>
                <p>En un entorno de producción, la IA generaría una respuesta personalizada basada en el contenido específico de tu prompt, utilizando modelos avanzados de procesamiento de lenguaje natural.</p>";
        }
    }
    
    /**
     * Procesa el envío final de un prompt para evaluación.
     */
    public function submitPrompt(Request $request, $exerciseId)
    {
        $request->validate([
            'prompt_text' => 'required|string|min:5|max:2000',
            'ai_response' => 'required|string'
        ]);
        
        $exercise = PromptExercise::with('lesson')->findOrFail($exerciseId);
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Solo los estudiantes pueden enviar prompts para evaluación.'
            ], 403);
        }
        
        try {
            // Contar intentos previos
            $attemptCount = PromptSubmission::where('exercise_id', $exerciseId)
                ->where('student_id', $user->user_id)
                ->count();
            
            // Crear nueva submission
            $submission = new PromptSubmission();
            $submission->exercise_id = $exerciseId;
            $submission->student_id = $user->user_id;
            $submission->prompt_text = $request->prompt_text;
            $submission->ai_response = $request->ai_response;
            $submission->attempt_number = $attemptCount + 1;
            
            // Evaluación automática del prompt
            $score = $this->evaluatePrompt($request->prompt_text, $request->ai_response, $exercise);
            $submission->score = $score;
            $submission->feedback = $this->generateFeedback($score, $request->prompt_text);
            $submission->status = 'graded'; // Podría ser 'submitted' si requiere revisión manual
            $submission->save();
            
            // Actualizar progreso de la lección
            $progress = PromptLessonProgress::firstOrCreate(
                [
                    'lesson_id' => $exercise->lesson_id,
                    'student_id' => $user->user_id
                ],
                [
                    'total_exercises' => $exercise->lesson->exercises->count(),
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'last_activity_at' => now()
                ]
            );
            
            // Si es el primer intento exitoso, incrementar ejercicios completados
            if ($attemptCount == 0 && $score >= 60) { // 60% como mínimo para considerar aprobado
                $progress->completed_exercises += 1;
                
                // Si completó todos los ejercicios, marcar como completada
                if ($progress->completed_exercises >= $progress->total_exercises) {
                    $progress->status = 'completed';
                    $progress->completed_at = now();
                    $progress->score = PromptSubmission::where('student_id', $user->user_id)
                        ->whereIn('exercise_id', $exercise->lesson->exercises->pluck('id'))
                        ->avg('score');
                }
            }
            
            $progress->last_activity_at = now();
            $progress->save();
            
            return response()->json([
                'success' => true,
                'score' => $score,
                'feedback' => $submission->feedback,
                'submission_id' => $submission->id,
                'message' => 'Tu prompt ha sido evaluado exitosamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al evaluar prompt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al evaluar el prompt. Inténtalo de nuevo.'
            ], 500);
        }
    }
    
    /**
     * Evalúa un prompt basado en criterios predefinidos.
     * En producción, esto podría utilizar un modelo de IA especializado.
     */
    private function evaluatePrompt($promptText, $aiResponse, $exercise)
    {
        // Criterios de evaluación básicos
        $criteria = [
            'length' => 0, // 0-20 puntos
            'clarity' => 0, // 0-25 puntos
            'specificity' => 0, // 0-25 puntos
            'creativity' => 0, // 0-20 puntos
            'relevance' => 0, // 0-10 puntos
        ];
        
        // Longitud del prompt (muy corto o muy largo reduce puntos)
        $length = strlen($promptText);
        if ($length < 20) {
            $criteria['length'] = 5;
        } elseif ($length < 50) {
            $criteria['length'] = 10;
        } elseif ($length < 300) {
            $criteria['length'] = 20;
        } elseif ($length < 500) {
            $criteria['length'] = 15;
        } else {
            $criteria['length'] = 10;
        }
        
        // Claridad (presencia de preguntas claras, instrucciones, etc.)
        if (strpos($promptText, '?') !== false) {
            $criteria['clarity'] += 10; // Tiene al menos una pregunta
        }
        
        $keyPhrases = ['por favor', 'quiero', 'necesito', 'explica', 'describe', 'cómo', 'qué es'];
        foreach ($keyPhrases as $phrase) {
            if (stripos($promptText, $phrase) !== false) {
                $criteria['clarity'] += 3;
            }
        }
        $criteria['clarity'] = min($criteria['clarity'], 25);
        
        // Especificidad
        $specificWords = ['específicamente', 'detalladamente', 'exactamente', 'precisamente'];
        foreach ($specificWords as $word) {
            if (stripos($promptText, $word) !== false) {
                $criteria['specificity'] += 5;
            }
        }
        
        // Si incluye formato específico
        if (stripos($promptText, 'lista') !== false || 
            stripos($promptText, 'pasos') !== false || 
            stripos($promptText, 'puntos') !== false) {
            $criteria['specificity'] += 10;
        }
        
        // Contexto adicional
        if (stripos($promptText, 'contexto') !== false || 
            stripos($promptText, 'ejemplo') !== false || 
            stripos($promptText, 'caso') !== false) {
            $criteria['specificity'] += 10;
        }
        
        $criteria['specificity'] = min($criteria['specificity'], 25);
        
        // Creatividad
        $creativeWords = ['imagina', 'crea', 'inventa', 'diseña', 'personaje', 'historia', 'escenario'];
        foreach ($creativeWords as $word) {
            if (stripos($promptText, $word) !== false) {
                $criteria['creativity'] += 5;
            }
        }
        
        // Si pide adoptar un rol o personaje
        if (stripos($promptText, 'eres') !== false || 
            stripos($promptText, 'actúa como') !== false || 
            stripos($promptText, 'simula') !== false) {
            $criteria['creativity'] += 10;
        }
        
        $criteria['creativity'] = min($criteria['creativity'], 20);
        
        // Relevancia al ejercicio
        $exerciseWords = array_merge(
            explode(' ', strtolower($exercise->title)),
            explode(' ', strtolower($exercise->description))
        );
        
        foreach ($exerciseWords as $word) {
            if (strlen($word) > 3 && stripos($promptText, $word) !== false) {
                $criteria['relevance'] += 2;
            }
        }
        
        $criteria['relevance'] = min($criteria['relevance'], 10);
        
        // Calcular puntuación total
        $totalScore = array_sum($criteria);
        
        // Si hay criterios específicos en la configuración del ejercicio, aplicarlos
        if (!empty($exercise->evaluation_criteria)) {
            // Implementación futura para criterios personalizados
        }
        
        return $totalScore;
    }
    
    /**
     * Genera feedback personalizado basado en la puntuación y el contenido del prompt.
     */
    private function generateFeedback($score, $promptText)
    {
        $feedback = "";
        
        if ($score >= 90) {
            $feedback = "¡Excelente trabajo! Tu prompt es muy efectivo y bien estructurado. ";
        } elseif ($score >= 70) {
            $feedback = "Buen trabajo. Tu prompt es bastante efectivo, aunque hay áreas de mejora. ";
        } elseif ($score >= 50) {
            $feedback = "Tu prompt es aceptable, pero necesita mejoras para ser más efectivo. ";
        } else {
            $feedback = "Tu prompt necesita trabajo sustancial para ser más efectivo. ";
        }
        
        // Feedback específico
        $length = strlen($promptText);
        if ($length < 50) {
            $feedback .= "Considera hacer tu prompt más detallado y específico. ";
        } elseif ($length > 500) {
            $feedback .= "Tu prompt es bastante largo. Intenta ser más conciso manteniendo los detalles importantes. ";
        }
        
        if (strpos($promptText, '?') === false) {
            $feedback .= "Incluir preguntas específicas puede mejorar la claridad de tu prompt. ";
        }
        
        if (stripos($promptText, 'por favor') === false && 
            stripos($promptText, 'quiero') === false && 
            stripos($promptText, 'necesito') === false) {
            $feedback .= "Expresar claramente lo que deseas puede mejorar las respuestas. ";
        }
        
        if (stripos($promptText, 'específicamente') === false && 
            stripos($promptText, 'detalladamente') === false) {
            $feedback .= "Pedir información específica ayuda a obtener respuestas más precisas. ";
        }
        
        return $feedback;
    }
} 