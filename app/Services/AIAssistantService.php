<?php

namespace App\Services;

use App\Models\AIAssistantPrompt;
use App\Models\ChallengeExercise;
use App\Models\TeachingChallenge;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AIAssistantService
{
    /**
     * API URL for OpenAI
     */
    protected $apiUrl = 'https://api.openai.com/v1/chat/completions';
    
    /**
     * API key for OpenAI
     */
    protected $apiKey;
    
    /**
     * Model to use for generation
     */
    protected $model = 'gpt-4o';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }
    
    /**
     * Generar ideas para desafíos.
     */
    public function generateChallengeIdeas($data)
    {
        // Implementación de la generación de ideas
        // Simulación de respuesta para desarrollo
        
        // Determinar cuántas ideas generar
        $count = isset($data['count']) ? intval($data['count']) : 3;
        $count = min(max($count, 1), 5); // Limitar entre 1 y 5 ideas
        
        // Crear array para almacenar ideas
        $ideas = [];
        
        // Generar ideas de ejemplo (esto sería reemplazado por la llamada a la API de IA)
        for ($i = 1; $i <= $count; $i++) {
            // Ejemplo para Python
            if ($data['challenge_type'] === 'python') {
                $ideas[] = [
                    'title' => "Desarrollo de un algoritmo de ordenamiento personalizado",
                    'description' => "Un desafío para comprender y aplicar conceptos de algoritmos de ordenamiento mediante la implementación de una versión personalizada que resuelva un problema específico.",
                    'objectives' => [
                        "Comprender cómo funcionan los algoritmos de ordenamiento básicos",
                        "Implementar un algoritmo de ordenamiento personalizado",
                        "Analizar la eficiencia de diferentes enfoques algorítmicos"
                    ],
                    'instructions' => "Los estudiantes deberán crear un algoritmo de ordenamiento que funcione con una estructura de datos específica. Primero analizarán algoritmos existentes como Bubble Sort y Quick Sort, y luego desarrollarán su propia variante para optimizar un caso particular.",
                    'materials' => [
                        "Entorno de desarrollo Python",
                        "Conjunto de datos de prueba",
                        "Guía de referencia de complejidad algorítmica"
                    ],
                    'evaluation_criteria' => [
                        "Corrección del algoritmo (40%)",
                        "Eficiencia en tiempo y espacio (30%)",
                        "Calidad del código y documentación (20%)",
                        "Análisis comparativo con otros algoritmos (10%)"
                    ],
                    'tips' => "Anima a los estudiantes a visualizar su algoritmo usando herramientas de trazado o animación para entender mejor su funcionamiento."
                ];
            } 
            // Ejemplo para AI Prompt
            else if ($data['challenge_type'] === 'ai_prompt') {
                $ideas[] = [
                    'title' => "Diseño de prompts para generación de contenido educativo",
                    'description' => "Un desafío para desarrollar habilidades de prompt engineering mediante la creación de prompts efectivos que generen contenido educativo de calidad.",
                    'objectives' => [
                        "Comprender los principios del prompt engineering",
                        "Diseñar prompts efectivos para fines educativos",
                        "Evaluar y mejorar prompts basándose en los resultados"
                    ],
                    'instructions' => "Los estudiantes deberán diseñar una serie de prompts para generar material educativo sobre un tema específico. Comenzarán con prompts básicos y los refinarán progresivamente para obtener resultados más precisos y útiles pedagógicamente.",
                    'materials' => [
                        "Acceso a un modelo de IA generativa",
                        "Guía de mejores prácticas de prompt engineering",
                        "Rúbrica de evaluación de contenido educativo"
                    ],
                    'evaluation_criteria' => [
                        "Claridad y precisión de los prompts (30%)",
                        "Calidad educativa del contenido generado (40%)",
                        "Proceso iterativo de mejora (20%)",
                        "Reflexión sobre limitaciones y ética (10%)"
                    ],
                    'tips' => "Recomienda a los estudiantes que empiecen con prompts sencillos y los vayan enriqueciendo con contexto, ejemplos y restricciones específicas."
                ];
            }
        }
        
        // Agregar variaciones para ideas múltiples
        if ($count > 1) {
            if ($data['challenge_type'] === 'python') {
                $ideas[] = [
                    'title' => "Creación de un sistema de recomendación simple",
                    'description' => "Un desafío para aplicar conceptos de análisis de datos mediante la implementación de un sistema de recomendación básico basado en similitud.",
                    'objectives' => [
                        "Implementar algoritmos de filtrado colaborativo",
                        "Trabajar con conjuntos de datos reales",
                        "Evaluar la precisión de las recomendaciones"
                    ],
                    'instructions' => "Los estudiantes desarrollarán un sistema de recomendación simple utilizando técnicas de filtrado colaborativo. Trabajarán con un conjunto de datos proporcionado y evaluarán diferentes métricas de similitud.",
                    'materials' => [
                        "Conjunto de datos de calificaciones de usuarios",
                        "Bibliotecas Pandas y NumPy",
                        "Plantilla de código inicial"
                    ],
                    'evaluation_criteria' => [
                        "Implementación correcta del algoritmo (35%)",
                        "Análisis de resultados (25%)",
                        "Optimización y eficiencia (20%)",
                        "Visualización de recomendaciones (20%)"
                    ],
                    'tips' => "Sugiere a los estudiantes comenzar con un enfoque de similitud de coseno antes de explorar métodos más avanzados."
                ];
                
                $ideas[] = [
                    'title' => "Desarrollo de un juego educativo en Python",
                    'description' => "Un proyecto para crear un juego educativo simple que refuerce conceptos específicos mientras enseña programación.",
                    'objectives' => [
                        "Aplicar principios de diseño de juegos a contextos educativos",
                        "Implementar mecánicas de juego en Python",
                        "Integrar contenido educativo en la experiencia de juego"
                    ],
                    'instructions' => "Los estudiantes diseñarán y desarrollarán un juego simple pero educativo utilizando bibliotecas como Pygame o creando una interfaz de texto. El juego debe enseñar o reforzar un concepto específico mientras es entretenido.",
                    'materials' => [
                        "Biblioteca Pygame (opcional)",
                        "Guía de diseño de juegos educativos",
                        "Ejemplos de juegos educativos exitosos"
                    ],
                    'evaluation_criteria' => [
                        "Valor educativo (40%)",
                        "Jugabilidad y diversión (25%)",
                        "Calidad del código (20%)",
                        "Creatividad e innovación (15%)"
                    ],
                    'tips' => "Anima a los estudiantes a probar sus juegos con usuarios reales y recopilar feedback durante el desarrollo."
                ];
            } 
            else if ($data['challenge_type'] === 'ai_prompt') {
                $ideas[] = [
                    'title' => "Creación de asistentes virtuales especializados",
                    'description' => "Un desafío para diseñar prompts que creen personajes de asistentes virtuales especializados en diferentes materias educativas.",
                    'objectives' => [
                        "Comprender cómo definir personalidad y conocimiento en un asistente virtual",
                        "Diseñar prompts para crear personas educativas específicas",
                        "Evaluar la efectividad pedagógica de diferentes enfoques"
                    ],
                    'instructions' => "Los estudiantes diseñarán una serie de prompts para crear asistentes virtuales especializados en diferentes materias. Definirán la personalidad, el estilo de enseñanza y las áreas de especialización de cada asistente.",
                    'materials' => [
                        "Acceso a un modelo de IA generativa",
                        "Ejemplos de perfiles de educadores efectivos",
                        "Plantillas para definir personas educativas"
                    ],
                    'evaluation_criteria' => [
                        "Claridad en la definición del asistente (30%)",
                        "Efectividad pedagógica (40%)",
                        "Consistencia de personalidad (20%)",
                        "Originalidad y creatividad (10%)"
                    ],
                    'tips' => "Sugiere a los estudiantes que piensen en sus mejores profesores y qué características los hacían efectivos como educadores."
                ];
                
                $ideas[] = [
                    'title' => "Análisis crítico de respuestas generadas por IA",
                    'description' => "Un desafío para desarrollar habilidades de evaluación crítica mediante el análisis de respuestas generadas por IA a partir de diferentes prompts.",
                    'objectives' => [
                        "Desarrollar habilidades de pensamiento crítico",
                        "Identificar sesgos o imprecisiones en contenido generado",
                        "Mejorar prompts basándose en análisis de resultados"
                    ],
                    'instructions' => "Los estudiantes crearán una serie de prompts sobre un tema controvertido o complejo, analizarán las respuestas generadas, identificarán problemas o sesgos, y rediseñarán los prompts para obtener respuestas más equilibradas y precisas.",
                    'materials' => [
                        "Acceso a un modelo de IA generativa",
                        "Guía de verificación de hechos",
                        "Recursos sobre pensamiento crítico"
                    ],
                    'evaluation_criteria' => [
                        "Profundidad del análisis crítico (40%)",
                        "Identificación de sesgos o problemas (30%)",
                        "Mejora efectiva de prompts (20%)",
                        "Reflexión sobre implicaciones éticas (10%)"
                    ],
                    'tips' => "Recomienda a los estudiantes que comparen respuestas de diferentes modelos o con diferentes configuraciones de parámetros."
                ];
            }
        }
        
        // Limitar al número pedido
        return array_slice($ideas, 0, $count);
    }
    
    /**
     * Generate exercise variants
     */
    public function generateExerciseVariants(ChallengeExercise $exercise, $count = 3)
    {
        // Obtener el prompt adecuado para generar variantes
        $prompt = AIAssistantPrompt::where('type', 'exercise_variant')
            ->where('is_active', true)
            ->where(function($query) use ($exercise) {
                $challenge = $exercise->challenge;
                if ($challenge) {
                    $query->where('category', $challenge->challenge_type)
                        ->orWhereNull('category');
                }
            })
            ->first();
            
        if (!$prompt) {
            // Si no hay prompt específico, usar uno genérico
            $prompt = AIAssistantPrompt::where('type', 'exercise_variant')
                ->where('is_active', true)
                ->whereNull('category')
                ->first();
                
            if (!$prompt) {
                throw new Exception('No hay prompts disponibles para generar variantes.');
            }
        }
        
        // Construir parámetros
        $params = [
            'title' => $exercise->title,
            'description' => $exercise->description,
            'instructions' => $exercise->instructions,
            'example_prompt' => $exercise->example_prompt ?? '',
            'count' => $count,
            'challenge_type' => $exercise->challenge->challenge_type ?? 'ai_prompt',
        ];
        
        // Formatear el prompt con los parámetros
        $formattedPrompt = $prompt->formatPrompt($params);
        
        // Hacer solicitud a la API
        return $this->makeRequest($formattedPrompt);
    }
    
    /**
     * Check quality and difficulty
     */
    public function checkQualityAndDifficulty($data)
    {
        // Implementación de la verificación de calidad
        // Simulación de respuesta para desarrollo
        return [
            'quality_score' => 85,
            'appropriate_difficulty' => true,
            'feedback' => [
                'strengths' => [
                    'Las instrucciones son claras y detalladas.',
                    'El ejercicio cumple con los objetivos didácticos especificados.',
                    'La estructura del ejercicio es lógica y coherente.'
                ],
                'areas_to_improve' => [
                    'Podrías añadir más ejemplos para facilitar la comprensión.',
                    'Considera incluir pistas escalonadas para estudiantes con dificultades.'
                ],
                'recommendations' => [
                    'Añade criterios de evaluación más específicos.',
                    'Incluye recursos adicionales para estudiantes avanzados.',
                    'Considera dividir la tarea en pasos más pequeños para los estudiantes principiantes.'
                ]
            ]
        ];
    }
    
    /**
     * Generar estructura para un desafío educativo.
     */
    public function generateChallengeStructure($data)
    {
        // Implementación de la generación de estructura
        // Simulación de respuesta para desarrollo
        
        $title = $data['title'];
        $topic = $data['topic'];
        $level = $data['level'];
        
        $html = "
            <div class='challenge-structure'>
                <h2>{$title}</h2>
                <p><strong>Tema:</strong> {$topic}</p>
                <p><strong>Nivel:</strong> {$level}</p>
                
                <h3>Objetivos de Aprendizaje</h3>
                <ul>
                    <li>Comprender los conceptos básicos de {$topic}</li>
                    <li>Desarrollar habilidades prácticas de resolución de problemas</li>
                    <li>Aplicar pensamiento computacional a situaciones reales</li>
                </ul>
                
                <h3>Descripción</h3>
                <p>Este desafío proporciona una experiencia práctica en {$topic}. Los estudiantes tendrán que aplicar conceptos teóricos para resolver problemas prácticos.</p>
                
                <h3>Requisitos Previos</h3>
                <ul>
                    <li>Conocimientos básicos de programación</li>
                    <li>Familiaridad con los fundamentos de {$topic}</li>
                </ul>
                
                <h3>Actividades</h3>
                <ol>
                    <li>Introducción a los conceptos clave (20 min)</li>
                    <li>Demostración de ejemplos prácticos (30 min)</li>
                    <li>Ejercicios guiados en grupos pequeños (45 min)</li>
                    <li>Desarrollo de solución individual (60 min)</li>
                    <li>Presentación y retroalimentación (30 min)</li>
                </ol>";
        
        // Añadir evaluación si se solicita
        if ($data['evaluation']) {
            $html .= "
                <h3>Criterios de Evaluación</h3>
                <ul>
                    <li>Funcionalidad (40%): La solución funciona correctamente según lo especificado</li>
                    <li>Eficiencia (20%): La solución utiliza los recursos de manera eficiente</li>
                    <li>Claridad (20%): El código/solución es legible y está bien documentado</li>
                    <li>Creatividad (20%): Se utilizan enfoques innovadores para resolver el problema</li>
                </ul>";
        }
        
        // Añadir diferenciación si se solicita
        if ($data['differentiation']) {
            $html .= "
                <h3>Estrategias de Diferenciación</h3>
                <ul>
                    <li><strong>Para estudiantes avanzados:</strong> Desafíos adicionales de optimización y extensión</li>
                    <li><strong>Para estudiantes con dificultades:</strong> Plantillas guiadas y puntos de control</li>
                    <li><strong>Soporte adicional:</strong> Recursos complementarios y ejemplos detallados</li>
                </ul>";
        }
        
        $html .= "
                <h3>Recursos Necesarios</h3>
                <ul>
                    <li>Ordenadores con acceso a internet</li>
                    <li>Software específico: editores de código, entornos de desarrollo</li>
                    <li>Materiales didácticos complementarios</li>
                </ul>
                
                <h3>Referencias y Material Complementario</h3>
                <ul>
                    <li>Guías de referencia de {$topic}</li>
                    <li>Tutoriales en línea</li>
                    <li>Ejemplos de proyectos similares</li>
                </ul>
            </div>
        ";
        
        // Versión en markdown
        $markdown = "# {$title}\n\n";
        $markdown .= "**Tema:** {$topic}  \n";
        $markdown .= "**Nivel:** {$level}\n\n";
        
        $markdown .= "## Objetivos de Aprendizaje\n";
        $markdown .= "* Comprender los conceptos básicos de {$topic}\n";
        $markdown .= "* Desarrollar habilidades prácticas de resolución de problemas\n";
        $markdown .= "* Aplicar pensamiento computacional a situaciones reales\n\n";
        
        $markdown .= "## Descripción\n";
        $markdown .= "Este desafío proporciona una experiencia práctica en {$topic}. Los estudiantes tendrán que aplicar conceptos teóricos para resolver problemas prácticos.\n\n";
        
        $markdown .= "## Requisitos Previos\n";
        $markdown .= "* Conocimientos básicos de programación\n";
        $markdown .= "* Familiaridad con los fundamentos de {$topic}\n\n";
        
        $markdown .= "## Actividades\n";
        $markdown .= "1. Introducción a los conceptos clave (20 min)\n";
        $markdown .= "2. Demostración de ejemplos prácticos (30 min)\n";
        $markdown .= "3. Ejercicios guiados en grupos pequeños (45 min)\n";
        $markdown .= "4. Desarrollo de solución individual (60 min)\n";
        $markdown .= "5. Presentación y retroalimentación (30 min)\n\n";
        
        // Añadir evaluación si se solicita
        if ($data['evaluation']) {
            $markdown .= "## Criterios de Evaluación\n";
            $markdown .= "* Funcionalidad (40%): La solución funciona correctamente según lo especificado\n";
            $markdown .= "* Eficiencia (20%): La solución utiliza los recursos de manera eficiente\n";
            $markdown .= "* Claridad (20%): El código/solución es legible y está bien documentado\n";
            $markdown .= "* Creatividad (20%): Se utilizan enfoques innovadores para resolver el problema\n\n";
        }
        
        // Añadir diferenciación si se solicita
        if ($data['differentiation']) {
            $markdown .= "## Estrategias de Diferenciación\n";
            $markdown .= "* **Para estudiantes avanzados:** Desafíos adicionales de optimización y extensión\n";
            $markdown .= "* **Para estudiantes con dificultades:** Plantillas guiadas y puntos de control\n";
            $markdown .= "* **Soporte adicional:** Recursos complementarios y ejemplos detallados\n\n";
        }
        
        $markdown .= "## Recursos Necesarios\n";
        $markdown .= "* Ordenadores con acceso a internet\n";
        $markdown .= "* Software específico: editores de código, entornos de desarrollo\n";
        $markdown .= "* Materiales didácticos complementarios\n\n";
        
        $markdown .= "## Referencias y Material Complementario\n";
        $markdown .= "* Guías de referencia de {$topic}\n";
        $markdown .= "* Tutoriales en línea\n";
        $markdown .= "* Ejemplos de proyectos similares\n";
        
        return [
            'html' => $html,
            'markdown' => $markdown
        ];
    }
    
    /**
     * Make API request to OpenAI
     */
    protected function makeRequest($prompt)
    {
        if (empty($this->apiKey)) {
            throw new Exception('API key not configured');
        }
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un asistente educativo experto en desarrollo de contenido didáctico para profesores. Debes crear contenido en español, claro y adaptado al nivel educativo solicitado.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            } else {
                Log::error('Error API IA: ' . $response->body());
                throw new Exception('Error en la comunicación con la API de IA: ' . $response->status());
            }
        } catch (Exception $e) {
            Log::error('Exception API IA: ' . $e->getMessage());
            throw new Exception('Error en la comunicación con la API de IA: ' . $e->getMessage());
        }
    }
} 