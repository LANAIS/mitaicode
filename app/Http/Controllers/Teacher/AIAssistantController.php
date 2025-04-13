<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AIAssistantPrompt;
use App\Models\ChallengeExercise;
use App\Models\TeachingChallenge;
use App\Services\AIAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class AIAssistantController extends Controller
{
    /**
     * Servicio de Asistente IA
     */
    protected $aiService;
    
    /**
     * Constructor
     */
    public function __construct(AIAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }
    
    /**
     * Verificar permisos
     */
    private function checkPermission()
    {
        $user = Auth::user();
        if ($user->role !== 'teacher' && $user->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para acceder al asistente de IA.');
        }
        return null;
    }
    
    /**
     * Mostrar el panel principal del asistente de IA.
     */
    public function index()
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        return view('teacher.ai_assistant.index');
    }
    
    /**
     * Mostrar el generador de ideas para desafíos.
     */
    public function showIdeaGenerator()
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        return view('teacher.ai_assistant.idea_generator');
    }
    
    /**
     * Generar ideas para desafíos.
     */
    public function generateIdeas(Request $request)
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'objectives' => 'required|string',
            'age_level' => 'nullable|string',
            'difficulty_level' => 'nullable|string|in:principiante,intermedio,avanzado',
            'challenge_type' => 'required|string|in:python,ai_prompt',
            'count' => 'nullable|integer|min:1|max:5',
        ]);
        
        try {
            $ideas = $this->aiService->generateChallengeIdeas([
                'subject' => $validated['subject'],
                'objectives' => $validated['objectives'],
                'age_level' => $validated['age_level'] ?? null,
                'difficulty_level' => $validated['difficulty_level'] ?? null,
                'challenge_type' => $validated['challenge_type'],
                'count' => $validated['count'] ?? 3,
            ]);
            
            return response()->json([
                'success' => true,
                'ideas' => $ideas
            ]);
        } catch (Exception $e) {
            Log::error('Error al generar ideas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'No se pudieron generar ideas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mostrar el generador de variantes de ejercicios.
     */
    public function showVariantGenerator()
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        $user = Auth::user();
        
        // Obtener desafíos del profesor
        $challenges = TeachingChallenge::where('teacher_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('teacher.ai_assistant.variant_generator', compact('challenges'));
    }
    
    /**
     * Obtener ejercicios para un desafío específico.
     */
    public function getExercises($challengeId)
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        $user = Auth::user();
        $challenge = TeachingChallenge::findOrFail($challengeId);
        
        // Verificar permisos
        if ($challenge->teacher_id !== $user->user_id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'No tienes permiso para ver estos ejercicios'
            ], 403);
        }
        
        $exercises = $challenge->exercises()->orderBy('order')->get();
        
        return response()->json([
            'success' => true,
            'exercises' => $exercises
        ]);
    }
    
    /**
     * Generar variantes de un ejercicio.
     */
    public function generateVariants(Request $request)
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'exercise_id' => 'required|exists:challenge_exercises,id',
            'count' => 'nullable|integer|min:1|max:5',
        ]);
        
        $user = Auth::user();
        $exercise = ChallengeExercise::with('challenge')->findOrFail($validated['exercise_id']);
        
        // Verificar permisos
        if ($exercise->challenge->teacher_id !== $user->user_id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'No tienes permiso para generar variantes de este ejercicio'
            ], 403);
        }
        
        try {
            $variants = $this->aiService->generateExerciseVariants(
                $exercise, 
                $validated['count'] ?? 3
            );
            
            return response()->json([
                'success' => true,
                'variants' => $variants
            ]);
        } catch (Exception $e) {
            Log::error('Error al generar variantes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'No se pudieron generar variantes: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mostrar el verificador de calidad.
     */
    public function showQualityChecker()
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        return view('teacher.ai_assistant.quality_checker');
    }
    
    /**
     * Mostrar el generador de estructura de desafíos.
     */
    public function showStructureGenerator()
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        return view('teacher.ai_assistant.structure_generator');
    }
    
    /**
     * Generar estructura para un desafío.
     */
    public function generateStructure(Request $request)
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'challenge_title' => 'required|string|max:150',
            'main_topic' => 'required|string|max:100',
            'educational_level' => 'required|string',
            'challenge_type' => 'required|string',
            'learning_objectives' => 'required|string',
            'time_allocation' => 'required|string',
            'required_resources' => 'nullable|string',
            'include_evaluation' => 'nullable|boolean',
            'include_differentiation' => 'nullable|boolean',
            'additional_notes' => 'nullable|string',
        ]);
        
        try {
            // Llamar al servicio de IA para generar la estructura
            $structure = $this->aiService->generateChallengeStructure([
                'title' => $validated['challenge_title'],
                'topic' => $validated['main_topic'],
                'level' => $validated['educational_level'],
                'type' => $validated['challenge_type'],
                'objectives' => $validated['learning_objectives'],
                'time' => $validated['time_allocation'],
                'resources' => $validated['required_resources'] ?? '',
                'evaluation' => $validated['include_evaluation'] ?? false,
                'differentiation' => $validated['include_differentiation'] ?? false,
                'notes' => $validated['additional_notes'] ?? '',
            ]);
            
            return response()->json([
                'success' => true,
                'html' => $structure['html'],
                'markdown' => $structure['markdown'],
                'title' => $validated['challenge_title']
            ]);
        } catch (Exception $e) {
            Log::error('Error al generar estructura: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'No se pudo generar la estructura: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verificar la calidad y dificultad de un ejercicio.
     */
    public function checkQuality(Request $request)
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'instructions' => 'required|string',
            'example_prompt' => 'nullable|string',
            'challenge_type' => 'required|string|in:python,ai_prompt',
            'difficulty_level' => 'required|string|in:principiante,intermedio,avanzado',
        ]);
        
        try {
            $feedback = $this->aiService->checkQualityAndDifficulty($validated);
            
            return response()->json([
                'success' => true,
                'feedback' => $feedback
            ]);
        } catch (Exception $e) {
            Log::error('Error al verificar calidad: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'No se pudo verificar la calidad: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Configuración de prompts del asistente de IA (solo para administradores).
     */
    public function showPrompts()
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'Solo los administradores pueden configurar los prompts del asistente.');
        }
        
        $prompts = AIAssistantPrompt::orderBy('type')->orderBy('category')->get();
        
        return view('teacher.ai_assistant.prompts', compact('prompts'));
    }
    
    /**
     * Guardar un nuevo prompt.
     */
    public function storePrompt(Request $request)
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'Solo los administradores pueden configurar los prompts del asistente.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:idea_generator,exercise_variant,quality_checker',
            'prompt_template' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string|in:python,ai_prompt',
            'difficulty_level' => 'nullable|string|in:principiante,intermedio,avanzado',
            'is_active' => 'nullable|boolean',
        ]);
        
        $prompt = new AIAssistantPrompt();
        $prompt->name = $validated['name'];
        $prompt->type = $validated['type'];
        $prompt->prompt_template = $validated['prompt_template'];
        $prompt->description = $validated['description'] ?? null;
        $prompt->category = $validated['category'] ?? null;
        $prompt->difficulty_level = $validated['difficulty_level'] ?? null;
        $prompt->is_active = $validated['is_active'] ?? true;
        $prompt->is_system = false;
        $prompt->created_by = $user->user_id;
        $prompt->save();
        
        return redirect()->route('teacher.ai_assistant.prompts')
            ->with('success', 'Prompt guardado con éxito.');
    }
    
    /**
     * Actualizar un prompt existente.
     */
    public function updatePrompt(Request $request, $id)
    {
        if ($redirect = $this->checkPermission()) {
            return $redirect;
        }
        
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'Solo los administradores pueden configurar los prompts del asistente.');
        }
        
        $prompt = AIAssistantPrompt::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'prompt_template' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string|in:python,ai_prompt',
            'difficulty_level' => 'nullable|string|in:principiante,intermedio,avanzado',
            'is_active' => 'nullable|boolean',
        ]);
        
        $prompt->name = $validated['name'];
        $prompt->prompt_template = $validated['prompt_template'];
        $prompt->description = $validated['description'] ?? null;
        $prompt->category = $validated['category'] ?? null;
        $prompt->difficulty_level = $validated['difficulty_level'] ?? null;
        $prompt->is_active = $validated['is_active'] ?? true;
        $prompt->save();
        
        return redirect()->route('teacher.ai_assistant.prompts')
            ->with('success', 'Prompt actualizado con éxito.');
    }
}
