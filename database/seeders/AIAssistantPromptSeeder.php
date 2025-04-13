<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AIAssistantPrompt;
use Illuminate\Support\Facades\DB;

class AIAssistantPromptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar prompts existentes
        DB::table('ai_assistant_prompts')->truncate();
        
        // Definir los prompts predefinidos del sistema
        
        // 1. Generador de ideas para desafíos de Python
        AIAssistantPrompt::create([
            'name' => 'Generador de Ideas de Desafíos Python',
            'type' => 'idea_generator',
            'category' => 'python',
            'is_system' => true,
            'is_active' => true,
            'description' => 'Genera ideas para desafíos de programación en Python adaptados a objetivos educativos específicos.',
            'prompt_template' => 'Genera {{count}} ideas innovadoras y atractivas para desafíos de programación en Python, considerando la siguiente información:

Tema: {{subject}}
Objetivos educativos: {{objectives}}
Nivel de edad: {{age_level}}
Nivel de dificultad: {{difficulty_level}}

Para cada idea, proporciona:
1. Título creativo
2. Descripción breve pero atractiva
3. Objetivos de aprendizaje específicos
4. Concepto principal que se trabaja
5. Estructura sugerida para el desafío (ejercicios progresivos)

Las ideas deben ser prácticas, interesantes y adaptadas al nivel de dificultad indicado. Evita ejemplos genéricos y proporciona detalles específicos que un profesor pueda utilizar directamente.

Estructura tu respuesta en formato JSON para facilitar su procesamiento, con este formato:
[
  {
    "titulo": "Título creativo",
    "descripcion": "Descripción breve",
    "objetivos": ["Objetivo 1", "Objetivo 2"],
    "concepto_principal": "Concepto que se trabaja",
    "estructura_sugerida": ["Ejercicio 1", "Ejercicio 2", "Ejercicio 3"]
  }
]',
        ]);
        
        // 2. Generador de ideas para desafíos de Prompting IA
        AIAssistantPrompt::create([
            'name' => 'Generador de Ideas de Desafíos de Prompting',
            'type' => 'idea_generator',
            'category' => 'ai_prompt',
            'is_system' => true,
            'is_active' => true,
            'description' => 'Genera ideas para desafíos de prompting de IA adaptados a objetivos educativos específicos.',
            'prompt_template' => 'Genera {{count}} ideas innovadoras y atractivas para desafíos de prompting de IA, considerando la siguiente información:

Tema: {{subject}}
Objetivos educativos: {{objectives}}
Nivel de edad: {{age_level}}
Nivel de dificultad: {{difficulty_level}}

Para cada idea, proporciona:
1. Título creativo
2. Descripción breve pero atractiva
3. Objetivos de aprendizaje específicos
4. Habilidades de prompting que se desarrollan
5. Estructura sugerida para el desafío (ejercicios progresivos)
6. Al menos 2 ejemplos de prompts que los estudiantes podrían crear

Las ideas deben ser prácticas, interesantes y adaptadas al nivel de dificultad indicado. Enfócate en enseñar técnicas efectivas de prompting para obtener resultados específicos de sistemas de IA.

Estructura tu respuesta en formato JSON para facilitar su procesamiento, con este formato:
[
  {
    "titulo": "Título creativo",
    "descripcion": "Descripción breve",
    "objetivos": ["Objetivo 1", "Objetivo 2"],
    "habilidades_prompting": ["Habilidad 1", "Habilidad 2"],
    "estructura_sugerida": ["Ejercicio 1", "Ejercicio 2", "Ejercicio 3"],
    "ejemplos_prompts": ["Ejemplo 1", "Ejemplo 2"]
  }
]',
        ]);
        
        // 3. Generador de variantes para ejercicios de Python
        AIAssistantPrompt::create([
            'name' => 'Generador de Variantes de Ejercicios Python',
            'type' => 'exercise_variant',
            'category' => 'python',
            'is_system' => true,
            'is_active' => true,
            'description' => 'Genera variantes de ejercicios existentes de Python manteniendo los objetivos de aprendizaje pero cambiando el contexto o dificultad.',
            'prompt_template' => 'Genera {{count}} variantes del siguiente ejercicio de Python, manteniendo los mismos objetivos de aprendizaje pero cambiando el contexto, los datos o el nivel de dificultad:

TÍTULO ORIGINAL: {{title}}
DESCRIPCIÓN: {{description}}
INSTRUCCIONES: {{instructions}}

Para cada variante, proporciona:
1. Un nuevo título descriptivo
2. Una nueva descripción adaptada al contexto
3. Nuevas instrucciones detalladas para los estudiantes
4. Un ejemplo de código inicial (si aplica)
5. Una breve explicación de cómo esta variante difiere del original pero mantiene los mismos objetivos de aprendizaje

Las variantes deben ser originales, atractivas y con un nivel de dificultad similar o progresivo. Asegúrate de que los conceptos principales a aprender sean los mismos, pero el contexto o enfoque sea diferente.

Estructura tu respuesta en formato JSON para facilitar su procesamiento:
[
  {
    "titulo": "Nuevo título",
    "descripcion": "Nueva descripción",
    "instrucciones": "Nuevas instrucciones",
    "codigo_inicial": "# Código inicial si aplica",
    "diferencias": "Explicación de las diferencias y similitudes"
  }
]',
        ]);
        
        // 4. Generador de variantes para ejercicios de Prompting
        AIAssistantPrompt::create([
            'name' => 'Generador de Variantes de Ejercicios de Prompting',
            'type' => 'exercise_variant',
            'category' => 'ai_prompt',
            'is_system' => true,
            'is_active' => true,
            'description' => 'Genera variantes de ejercicios existentes de prompting de IA manteniendo los objetivos de aprendizaje pero cambiando el contexto o dificultad.',
            'prompt_template' => 'Genera {{count}} variantes del siguiente ejercicio de prompting de IA, manteniendo los mismos objetivos de aprendizaje pero cambiando el contexto, el enfoque o el nivel de dificultad:

TÍTULO ORIGINAL: {{title}}
DESCRIPCIÓN: {{description}}
INSTRUCCIONES: {{instructions}}
EJEMPLO DE PROMPT: {{example_prompt}}

Para cada variante, proporciona:
1. Un nuevo título descriptivo
2. Una nueva descripción adaptada al contexto
3. Nuevas instrucciones detalladas para los estudiantes
4. Un nuevo ejemplo de prompt que muestre el resultado esperado
5. Una breve explicación de cómo esta variante difiere del original pero mantiene los mismos objetivos de aprendizaje

Las variantes deben ser originales, atractivas y con un nivel de dificultad similar o progresivo. Asegúrate de que las técnicas de prompting a aprender sean las mismas, pero el contexto o enfoque sea diferente.

Estructura tu respuesta en formato JSON para facilitar su procesamiento:
[
  {
    "titulo": "Nuevo título",
    "descripcion": "Nueva descripción",
    "instrucciones": "Nuevas instrucciones",
    "ejemplo_prompt": "Nuevo ejemplo de prompt",
    "diferencias": "Explicación de las diferencias y similitudes"
  }
]',
        ]);
        
        // 5. Verificador de calidad para ejercicios de Python
        AIAssistantPrompt::create([
            'name' => 'Verificador de Calidad para Ejercicios Python',
            'type' => 'quality_checker',
            'category' => 'python',
            'is_system' => true,
            'is_active' => true,
            'description' => 'Analiza la calidad, claridad y adecuación del nivel de dificultad de ejercicios de Python.',
            'prompt_template' => 'Evalúa la calidad del siguiente ejercicio de programación en Python para nivel {{difficulty_level}}:

TÍTULO: {{title}}
DESCRIPCIÓN: {{description}}
INSTRUCCIONES: {{instructions}}

Realiza un análisis detallado que incluya:

1. CALIDAD GENERAL (1-10):
   - Justificación de la puntuación asignada

2. CLARIDAD (1-10):
   - ¿Son las instrucciones claras y comprensibles?
   - ¿Qué partes podrían ser confusas para el estudiante?
   - Sugerencias para mejorar la claridad

3. ADECUACIÓN AL NIVEL {{difficulty_level}} (1-10):
   - ¿Es apropiado para el nivel indicado?
   - Si no es adecuado, ¿qué nivel sería más apropiado?
   - Sugerencias para ajustar la dificultad

4. VALOR PEDAGÓGICO (1-10):
   - ¿Qué conceptos de programación se practican?
   - ¿El ejercicio promueve el pensamiento crítico?
   - ¿Proporciona un reto interesante para los estudiantes?

5. MEJORAS SUGERIDAS:
   - Cambios específicos para mejorar el ejercicio
   - Elementos adicionales que podrían incluirse
   - Cómo hacer el ejercicio más atractivo o relevante

Proporciona un análisis detallado y constructivo que ayude a mejorar la calidad del ejercicio manteniendo sus objetivos de aprendizaje.

Asegúrate de incluir ejemplos concretos y sugerencias prácticas que un profesor pueda implementar directamente.',
        ]);
        
        // 6. Verificador de calidad para ejercicios de Prompting
        AIAssistantPrompt::create([
            'name' => 'Verificador de Calidad para Ejercicios de Prompting',
            'type' => 'quality_checker',
            'category' => 'ai_prompt',
            'is_system' => true,
            'is_active' => true,
            'description' => 'Analiza la calidad, claridad y adecuación del nivel de dificultad de ejercicios de prompting de IA.',
            'prompt_template' => 'Evalúa la calidad del siguiente ejercicio de prompting de IA para nivel {{difficulty_level}}:

TÍTULO: {{title}}
DESCRIPCIÓN: {{description}}
INSTRUCCIONES: {{instructions}}
EJEMPLO DE PROMPT: {{example_prompt}}

Realiza un análisis detallado que incluya:

1. CALIDAD GENERAL (1-10):
   - Justificación de la puntuación asignada

2. CLARIDAD (1-10):
   - ¿Son las instrucciones claras y comprensibles?
   - ¿Qué partes podrían ser confusas para el estudiante?
   - Sugerencias para mejorar la claridad

3. ADECUACIÓN AL NIVEL {{difficulty_level}} (1-10):
   - ¿Es apropiado para el nivel indicado?
   - Si no es adecuado, ¿qué nivel sería más apropiado?
   - Sugerencias para ajustar la dificultad

4. VALOR PEDAGÓGICO (1-10):
   - ¿Qué técnicas de prompting se practican?
   - ¿El ejercicio promueve la creatividad y el pensamiento crítico?
   - ¿Proporciona un reto interesante para los estudiantes?

5. EJEMPLO DE PROMPT (1-10):
   - ¿Es el ejemplo claro y representativo?
   - ¿Demuestra efectivamente las técnicas que se pretenden enseñar?
   - Sugerencias para mejorar el ejemplo

6. MEJORAS SUGERIDAS:
   - Cambios específicos para mejorar el ejercicio
   - Elementos adicionales que podrían incluirse
   - Cómo hacer el ejercicio más atractivo o relevante

Proporciona un análisis detallado y constructivo que ayude a mejorar la calidad del ejercicio manteniendo sus objetivos de aprendizaje.

Asegúrate de incluir ejemplos concretos y sugerencias prácticas que un profesor pueda implementar directamente.',
        ]);
    }
}
