<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

/**
 * Clase que proporciona métodos para interactuar con la API de OpenAI
 */
class OpenAIService {
    private $api_key;
    private $organization_id;
    private $default_model;
    private $default_image_model;
    private $default_code_model;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Cargar configuración desde el archivo .env de Laravel
        $this->api_key = env('OPENAI_API_KEY', '');
        $this->organization_id = env('OPENAI_ORGANIZATION_ID', '');
        $this->default_model = env('OPENAI_DEFAULT_MODEL', 'gpt-3.5-turbo');
        $this->default_image_model = env('OPENAI_DEFAULT_IMAGE_MODEL', 'dall-e-3');
        $this->default_code_model = env('OPENAI_DEFAULT_CODE_MODEL', 'gpt-4');

        // Si no hay API key, registrar error
        if (empty($this->api_key)) {
            Log::warning('OpenAI API Key no configurada. Establecer OPENAI_API_KEY en el archivo .env');
        } else {
            // Registrar información sobre la API key (solo para depuración, mostrando solo los primeros 10 caracteres)
            $keyLength = strlen($this->api_key);
            $maskedKey = substr($this->api_key, 0, 10) . '...[' . ($keyLength - 10) . ' caracteres más]';
            Log::info('OpenAI API Key configurada: ' . $maskedKey);
        }
    }
    
    /**
     * Realiza una solicitud a la API de OpenAI para generar texto
     * 
     * @param string $prompt El prompt para la generación
     * @param array $options Opciones adicionales para la solicitud
     * @param int $user_id ID del usuario que realiza la solicitud (opcional)
     * @return array Respuesta procesada
     */
    public function generateText($prompt, $options = [], $user_id = null) {
        try {
            // Configurar opciones predeterminadas
            $default_options = [
                'model' => $this->default_model,
                'temperature' => 0.7,
                'max_tokens' => 1000,
                'top_p' => 1.0,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 0.0
            ];
            
            // Fusionar opciones proporcionadas con las predeterminadas
            $options = array_merge($default_options, $options);
            
            // Preparar el cuerpo de la solicitud
            $request_body = [
                'model' => $options['model'],
                'temperature' => (float)$options['temperature'],
                'max_tokens' => (int)$options['max_tokens'],
                'top_p' => (float)$options['top_p'],
                'frequency_penalty' => (float)$options['frequency_penalty'],
                'presence_penalty' => (float)$options['presence_penalty'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $options['system_message'] ?? 'Eres un asistente útil y educativo diseñado para ayudar a estudiantes a aprender sobre inteligencia artificial.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ];
            
            // Si hay un historial de mensajes, reemplazar los mensajes predeterminados
            if (isset($options['messages']) && is_array($options['messages'])) {
                $request_body['messages'] = $options['messages'];
            }
            
            // Log del prompt completo (para depuración)
            Log::info("Generando texto con OpenAI. Modelo: " . $options['model'] . ", Temperatura: " . $options['temperature']);
            
            // Realizar solicitud a la API
            $response = $this->makeApiRequest('https://api.openai.com/v1/chat/completions', $request_body);
            
            return $this->processTextResponse($response);
        } catch (\Exception $e) {
            Log::error("Excepción en generateText: " . $e->getMessage());
            return [
                'error' => true,
                'message' => "Error al generar texto: " . $e->getMessage(),
                'content' => null
            ];
        }
    }
    
    /**
     * Analiza y evalúa código proporcionado por un estudiante
     * 
     * @param string $code El código a analizar
     * @param string $instructions Instrucciones sobre qué evaluar
     * @param array $options Opciones adicionales para la solicitud
     * @return array Respuesta procesada
     */
    public function analyzeCode($code, $instructions, $options = []) {
        try {
            // Configurar prompt para evaluación de código
            $prompt = "Analiza el siguiente código y proporciona retroalimentación detallada basada en estas instrucciones: $instructions\n\nCódigo a analizar:\n```\n$code\n```";
            
            // Configurar opciones predeterminadas
            $default_options = [
                'model' => $this->default_code_model,
                'temperature' => 0.3,
                'max_tokens' => 1500,
                'top_p' => 1.0,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 0.0,
                'system_message' => 'Eres un evaluador experto en programación. Tu tarea es analizar y evaluar código proporcionado por estudiantes de manera objetiva y constructiva.'
            ];
            
            // Fusionar opciones proporcionadas con las predeterminadas
            $options = array_merge($default_options, $options);
            
            // Log específico para análisis de código
            Log::info("Analizando código con OpenAI. Modelo: " . $options['model'] . ", Longitud del código: " . strlen($code));
            
            return $this->generateText($prompt, $options);
        } catch (\Exception $e) {
            Log::error("Excepción en analyzeCode: " . $e->getMessage());
            return [
                'error' => true,
                'message' => "Error al analizar código: " . $e->getMessage(),
                'content' => null
            ];
        }
    }
    
    /**
     * Realiza una solicitud HTTP a la API de OpenAI
     * 
     * @param string $url URL del endpoint de la API
     * @param array $body Cuerpo de la solicitud
     * @return array|string Respuesta de la API
     */
    private function makeApiRequest($url, $body) {
        try {
            Log::info("OpenAI API Request: " . substr(json_encode($body), 0, 200) . "...");
            
            $ch = curl_init($url);
            
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api_key
            ];
            
            if (!empty($this->organization_id)) {
                $headers[] = 'OpenAI-Organization: ' . $this->organization_id;
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Timeout aumentado a 60 segundos
            
            // Ignorar verificación SSL (solución temporal para entornos de desarrollo)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            
            Log::info("Enviando solicitud a OpenAI API...");
            $response = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Verificar si hubo un error de curl
            if ($response === false) {
                $curl_error = curl_error($ch);
                curl_close($ch);
                Log::error('Error en curl al comunicarse con OpenAI: ' . $curl_error);
                return [
                    'error' => true,
                    'message' => 'Error de conexión: ' . $curl_error,
                    'status_code' => 0
                ];
            }
            
            curl_close($ch);
            Log::info("Respuesta recibida de OpenAI API. Status code: " . $status_code);
            
            if ($status_code != 200) {
                // Manejar errores
                $error = json_decode($response, true);
                $errorMsg = isset($error['error']['message']) ? $error['error']['message'] : 'Error desconocido';
                Log::error('Error en respuesta de OpenAI: ' . $errorMsg . ' (Status code: ' . $status_code . ')');
                Log::error('Respuesta completa: ' . json_encode($error));
                return [
                    'error' => true,
                    'message' => $errorMsg,
                    'status_code' => $status_code
                ];
            }
            
            // Log de respuesta exitosa (parte inicial)
            Log::info("Respuesta exitosa recibida. Longitud: " . strlen($response));
            
            return json_decode($response, true);
        } catch (\Exception $e) {
            Log::error('Excepción al comunicarse con OpenAI: ' . $e->getMessage());
            Log::error('Detalles: ' . $e->getTraceAsString());
            return [
                'error' => true,
                'message' => 'Error interno: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }
    
    /**
     * Procesa la respuesta de texto de la API
     * 
     * @param array $response Respuesta de la API
     * @return array Respuesta procesada
     */
    private function processTextResponse($response) {
        if (isset($response['error']) && $response['error'] === true) {
            Log::error("Error en processTextResponse: " . ($response['message'] ?? 'Error desconocido'));
            return $response;
        }
        
        // Verificar si la respuesta tiene la estructura esperada
        if (!isset($response['choices']) || !is_array($response['choices']) || empty($response['choices'])) {
            Log::error("Error: No se encontraron 'choices' en la respuesta de OpenAI");
            Log::error("Respuesta completa: " . json_encode($response));
            return [
                'error' => true,
                'message' => 'Respuesta de OpenAI incompleta o inválida (no se encontraron "choices")',
                'content' => null
            ];
        }
        
        if (!isset($response['choices'][0]['message']) || !isset($response['choices'][0]['message']['content'])) {
            Log::error("Error: No se encontró 'message' o 'content' en la respuesta de OpenAI");
            Log::error("Respuesta completa: " . json_encode($response));
            return [
                'error' => true,
                'message' => 'Respuesta de OpenAI incompleta o inválida (no se encontró "message.content")',
                'content' => null
            ];
        }
        
        $content = $response['choices'][0]['message']['content'] ?? '';
        if (empty($content)) {
            Log::warning("Advertencia: Contenido vacío en la respuesta de OpenAI");
        } else {
            Log::info("Contenido extraído correctamente. Longitud: " . strlen($content));
        }
        
        return [
            'error' => false,
            'content' => $content,
            'finish_reason' => $response['choices'][0]['finish_reason'] ?? 'unknown',
            'model' => $response['model'] ?? $this->default_model,
            'usage' => $response['usage'] ?? null
        ];
    }
} 