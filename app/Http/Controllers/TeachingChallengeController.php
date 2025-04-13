<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\TeachingChallenge;
use App\Models\ChallengeExercise;
use App\Models\ChallengeStudentProgress;
use App\Models\ExerciseSubmission;
use App\Models\ChallengeAnalytic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeachingChallengeController extends Controller
{
    /**
     * Muestra la lista de desafíos para el profesor.
     */
    public function index()
    {
        $user = Auth::user();
        $challenges = collect();
        
        if ($user->role === 'teacher') {
            $challenges = TeachingChallenge::where('teacher_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->role === 'admin') {
            $challenges = TeachingChallenge::orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('challenges.index', compact('challenges'));
    }

    /**
     * Muestra la lista de desafíos para una clase específica.
     */
    public function indexForClass($classId)
    {
        $user = Auth::user();
        $classroom = Classroom::findOrFail($classId);
        
        // Verificar permiso
        if ($user->role !== 'admin' && $classroom->teacher_id !== $user->user_id) {
            return redirect()->route('classrooms.index')->with('error', 'No tienes permiso para ver los desafíos de esta clase.');
        }
        
        $challenges = TeachingChallenge::where('class_id', $classId)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('challenges.class_index', compact('challenges', 'classroom'));
    }

    /**
     * Muestra el formulario para crear un nuevo desafío.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'teacher' && $user->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para crear desafíos.');
        }
        
        $classrooms = Classroom::where('teacher_id', $user->user_id)
            ->where('is_active', true)
            ->orderBy('class_name')
            ->get();
        
        return view('challenges.create', compact('classrooms'));
    }

    /**
     * Almacena un nuevo desafío en la base de datos.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'teacher' && $user->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para crear desafíos.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'objectives' => 'required|string',
            'instructions' => 'required|string',
            'class_id' => 'nullable|exists:classrooms,class_id',
            'is_public' => 'boolean',
            'challenge_type' => ['required', Rule::in(['python', 'ai_prompt'])],
            'difficulty' => ['required', Rule::in(['principiante', 'intermedio', 'avanzado'])],
            'estimated_time' => 'nullable|integer|min:1',
            'points' => 'nullable|integer|min:0',
            'evaluation_criteria' => 'nullable|string',
            'solution_guide' => 'nullable|string',
        ]);
        
        // Si no es público y no tiene clase, asignar a una de las clases del profesor
        if (empty($validated['class_id']) && empty($validated['is_public'])) {
            return redirect()->back()->withInput()->with('error', 'Debes asignar el desafío a una clase o marcarlo como público.');
        }
        
        // Crear el desafío
        $challenge = new TeachingChallenge();
        $challenge->title = $validated['title'];
        $challenge->description = $validated['description'];
        $challenge->objectives = $validated['objectives'];
        $challenge->instructions = $validated['instructions'];
        $challenge->teacher_id = $user->user_id;
        $challenge->class_id = $validated['class_id'] ?? null;
        $challenge->is_public = $validated['is_public'] ?? false;
        $challenge->status = 'draft';
        $challenge->challenge_type = $validated['challenge_type'];
        $challenge->difficulty = $validated['difficulty'];
        $challenge->estimated_time = $validated['estimated_time'] ?? null;
        $challenge->points = $validated['points'] ?? 100;
        $challenge->order = 0; // Por defecto
        
        // Procesar criterios de evaluación si existen
        if (!empty($validated['evaluation_criteria'])) {
            $criteria = explode("\n", $validated['evaluation_criteria']);
            $challenge->evaluation_criteria = json_encode(array_map('trim', $criteria));
        }
        
        $challenge->solution_guide = $validated['solution_guide'] ?? null;
        $challenge->save();
        
        return redirect()->route('challenges.edit', $challenge->id)
            ->with('success', 'Desafío creado con éxito. Ahora puedes añadir ejercicios.');
    }

    /**
     * Muestra los detalles del desafío.
     */
    public function show($id)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::with(['exercises' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($id);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para ver este desafío.');
        }
        
        return view('challenges.show', compact('challenge'));
    }

    /**
     * Muestra el formulario para editar un desafío.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::findOrFail($id);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para editar este desafío.');
        }
        
        $classrooms = Classroom::where('teacher_id', $user->user_id)
            ->where('is_active', true)
            ->orderBy('class_name')
            ->get();
        
        $exercises = $challenge->exercises()->orderBy('order')->get();
        
        return view('challenges.edit', compact('challenge', 'classrooms', 'exercises'));
    }

    /**
     * Actualiza el desafío en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::findOrFail($id);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para editar este desafío.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'objectives' => 'required|string',
            'instructions' => 'required|string',
            'class_id' => 'nullable|exists:classrooms,class_id',
            'is_public' => 'boolean',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'challenge_type' => ['required', Rule::in(['python', 'ai_prompt'])],
            'difficulty' => ['required', Rule::in(['principiante', 'intermedio', 'avanzado'])],
            'estimated_time' => 'nullable|integer|min:1',
            'points' => 'nullable|integer|min:0',
            'evaluation_criteria' => 'nullable|string',
            'solution_guide' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);
        
        // Si no es público y no tiene clase, asignar a una de las clases del profesor
        if (empty($validated['class_id']) && empty($validated['is_public'])) {
            return redirect()->back()->withInput()->with('error', 'Debes asignar el desafío a una clase o marcarlo como público.');
        }
        
        // Actualizar el desafío
        $challenge->title = $validated['title'];
        $challenge->description = $validated['description'];
        $challenge->objectives = $validated['objectives'];
        $challenge->instructions = $validated['instructions'];
        $challenge->class_id = $validated['class_id'] ?? null;
        $challenge->is_public = $validated['is_public'] ?? false;
        $challenge->status = $validated['status'];
        $challenge->challenge_type = $validated['challenge_type'];
        $challenge->difficulty = $validated['difficulty'];
        $challenge->estimated_time = $validated['estimated_time'] ?? null;
        $challenge->points = $validated['points'] ?? 100;
        
        if (isset($validated['order'])) {
            $challenge->order = $validated['order'];
        }
        
        // Procesar criterios de evaluación si existen
        if (!empty($validated['evaluation_criteria'])) {
            $criteria = explode("\n", $validated['evaluation_criteria']);
            $challenge->evaluation_criteria = json_encode(array_map('trim', $criteria));
        } else {
            $challenge->evaluation_criteria = null;
        }
        
        $challenge->solution_guide = $validated['solution_guide'] ?? null;
        $challenge->save();
        
        return redirect()->route('challenges.edit', $challenge->id)
            ->with('success', 'Desafío actualizado con éxito.');
    }

    /**
     * Elimina el desafío de la base de datos.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::findOrFail($id);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para eliminar este desafío.');
        }
        
        // Eliminar el desafío y todos sus ejercicios, progreso y analíticas (cascade delete)
        $challenge->delete();
        
        return redirect()->route('challenges.index')
            ->with('success', 'Desafío eliminado con éxito.');
    }
    
    /**
     * Cambia el estado del desafío (publicar, archivar, borrador).
     */
    public function changeStatus(Request $request, $id)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::findOrFail($id);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para cambiar el estado de este desafío.');
        }
        
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);
        
        $challenge->status = $validated['status'];
        $challenge->save();
        
        $statusTexts = [
            'draft' => 'borrador',
            'published' => 'publicado',
            'archived' => 'archivado'
        ];
        
        return redirect()->back()
            ->with('success', 'Desafío ' . $statusTexts[$validated['status']] . ' con éxito.');
    }
    
    /**
     * Vista previa del desafío como estudiante.
     */
    public function preview($id)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::with(['exercises' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($id);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para previsualizar este desafío.');
        }
        
        return view('challenges.preview', compact('challenge'));
    }
    
    /**
     * Página de analíticas del desafío.
     */
    public function analytics($id)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::with(['analytics', 'studentProgress', 'exercises'])->findOrFail($id);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para ver las analíticas de este desafío.');
        }
        
        // Asegurarse de que exista un registro de analíticas
        $analytics = $challenge->analytics;
        if (!$analytics) {
            $analytics = ChallengeAnalytic::updateForChallenge($challenge->id);
            // Si no se pudo crear el analytics, crear un objeto temporal con valores por defecto
            if (!$analytics || $analytics === true) {
                $analytics = new ChallengeAnalytic();
                $analytics->challenge_id = $challenge->id;
                $analytics->total_students = 0;
                $analytics->started_count = 0;
                $analytics->completed_count = 0;
                $analytics->average_score = 0;
                $analytics->average_time_minutes = 0;
            }
        }
        
        return view('challenges.analytics', compact('challenge', 'analytics'));
    }
    
    /**
     * Actualiza las analíticas del desafío.
     */
    public function updateAnalytics($id)
    {
        $user = Auth::user();
        $challenge = TeachingChallenge::findOrFail($id);
        
        // Verificar permisos
        if ($user->role !== 'admin' && $challenge->teacher_id !== $user->user_id) {
            return redirect()->route('challenges.index')
                ->with('error', 'No tienes permiso para actualizar las analíticas de este desafío.');
        }
        
        ChallengeAnalytic::updateForChallenge($challenge->id);
        
        return redirect()->route('challenges.analytics', $challenge->id)
            ->with('success', 'Analíticas actualizadas con éxito.');
    }
}
