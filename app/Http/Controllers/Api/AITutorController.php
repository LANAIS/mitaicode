<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Session;

class AITutorController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Procesar una pregunta del estudiante al tutor IA
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'challenge_type' => 'required|string|in:python,prompts',
            'exercise_title' => 'required|string',
            'conversation_id' => 'nullable|string',
        ]);

        try {
            $question = $request->question;
            $challengeType = $request->challenge_type;
            $exerciseTitle = $request->exercise_title;
            $conversationId = $request->conversation_id ?? uniqid('conv_');
            
            // Obtener historial de conversación o iniciar uno nuevo
            $chatHistory = Session::get('ai_tutor_chat_'.$conversationId, []);
            
            // Construir un mensaje de sistema con contexto específico para el ejercicio
            $systemMessage = $this->buildSystemMessage($challengeType, $exerciseTitle);
            
            // Preparar mensajes para OpenAI
            $messages = [
                [
                    'role' => 'system',
                    'content' => $systemMessage
                ]
            ];
            
            // Añadir historial de conversación (máximo 5 intercambios para evitar tokens excesivos)
            $recentHistory = array_slice($chatHistory, -10, 10);
            $messages = array_merge($messages, $recentHistory);
            
            // Añadir la pregunta actual
            $messages[] = [
                'role' => 'user',
                'content' => $question
            ];
            
            // Configurar opciones para la API
            $options = [
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ];
            
            // Realizar la llamada a OpenAI
            $response = $this->openAIService->generateText('', $options);
            
            if (isset($response['error']) && $response['error']) {
                Log::error('Error en OpenAI: ' . $response['message']);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar tu pregunta: ' . $response['message']
                ], 500);
            }
            
            // Obtener la respuesta
            $aiResponse = $response['content'] ?? 'Lo siento, no puedo responder en este momento.';
            
            // Añadir esta interacción al historial
            $chatHistory[] = [
                'role' => 'user',
                'content' => $question
            ];
            
            $chatHistory[] = [
                'role' => 'assistant',
                'content' => $aiResponse
            ];
            
            // Guardar historial actualizado
            Session::put('ai_tutor_chat_'.$conversationId, $chatHistory);
            
            return response()->json([
                'success' => true,
                'response' => $aiResponse,
                'category' => $this->determineCategory($question),
                'conversation_id' => $conversationId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en AI Tutor: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Lo siento, ocurrió un error al procesar tu pregunta. Por favor, inténtalo de nuevo.'
            ], 500);
        }
    }
    
    /**
     * Construir un mensaje de sistema con contexto específico para el tipo de desafío
     */
    private function buildSystemMessage($challengeType, $exerciseTitle)
    {
        $baseMessage = "Eres un tutor de IA especializado en ayudar a estudiantes con ejercicios de programación. ";
        
        if ($challengeType == 'python') {
            $baseMessage .= "Tu especialidad es Python y estás ayudando con el ejercicio: '$exerciseTitle'. ";
            $baseMessage .= "Debes proporcionar explicaciones claras, ejemplos prácticos y guiar al estudiante sin resolver completamente el ejercicio. ";
            $baseMessage .= "Cuando sea apropiado, muestra ejemplos de código en Python usando bloques de código con formato adecuado. ";
            $baseMessage .= "Si el estudiante pregunta sobre conceptos específicos de Python (variables, funciones, bucles, etc.), proporciónale explicaciones detalladas. ";
            $baseMessage .= "Si el estudiante tiene un error de código, ayúdale a solucionarlo por sí mismo con pistas y explicaciones.";
        } else {
            $baseMessage .= "Tu especialidad es el diseño de prompts para IA y estás ayudando con el ejercicio: '$exerciseTitle'. ";
            $baseMessage .= "Debes proporcionar consejos sobre buenas prácticas para crear prompts efectivos, estructura, elementos clave y ejemplos. ";
            $baseMessage .= "Si el estudiante pregunta sobre conceptos específicos de prompting (contexto, instrucciones, limitaciones, etc.), proporciónale explicaciones detalladas. ";
            $baseMessage .= "Cuando sea apropiado, muestra ejemplos de prompts bien estructurados con formato adecuado. ";
            $baseMessage .= "Recuerda que un buen prompt debe tener contexto claro, instrucciones específicas, y detalles sobre el formato o estilo deseado.";
        }
        
        $baseMessage .= "\n\nResponde siempre en español y utiliza HTML para formatear tus respuestas. Puedes usar <p>, <ul>, <li>, <code>, <pre>, <strong>, <h6> y otros elementos HTML para mejorar la legibilidad.";
        
        return $baseMessage;
    }
    
    /**
     * Determinar la categoría de la pregunta para mostrar etiqueta visual
     */
    private function determineCategory($question)
    {
        if (preg_match('/(empie|empeza|comienz|start)/i', $question)) {
            return 'Inicio';
        } else if (preg_match('/(pista|ayuda|hint|help)/i', $question)) {
            return 'Pista';
        } else if (preg_match('/(bucle|loop|for|while)/i', $question)) {
            return 'Bucles';
        } else if (preg_match('/(funcion|función|def|method)/i', $question)) {
            return 'Funciones';
        } else if (preg_match('/(error|fallo|problema|issue|bug)/i', $question)) {
            return 'Error';
        } else if (preg_match('/(variable|tipo|dato|type|data)/i', $question)) {
            return 'Variables';
        } else if (preg_match('/(lista|array|vector|colección)/i', $question)) {
            return 'Estructuras';
        } else if (preg_match('/(prompt|ia|inteligencia|artificial)/i', $question)) {
            return 'Prompts IA';
        } else if (preg_match('/(concepto|entender|clave|concept|understand)/i', $question)) {
            return 'Conceptos';
        } else {
            return 'Consulta';
        }
    }
}
