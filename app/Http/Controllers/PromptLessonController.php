<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Classroom;
use App\Models\PromptLesson;
use App\Models\PromptExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PromptLessonController extends Controller
{
    /**
     * Muestra un listado de las lecciones de prompt engineering del profesor.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'teacher') {
            $lessons = PromptLesson::byTeacher($user->user_id)
                ->with('classroom')
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->role === 'admin') {
            $lessons = PromptLesson::with('classroom')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Estudiantes solo ven lecciones públicas o asignadas a sus clases
            $studentClassIds = $user->studentEnrollments->pluck('class_id')->toArray();
            
            $lessons = PromptLesson::where(function($query) use ($studentClassIds) {
                    $query->whereIn('class_id', $studentClassIds)
                        ->orWhere('is_public', true);
                })
                ->with('classroom')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('prompt_lessons.index', compact('lessons'));
    }

    /**
     * Muestra el formulario para crear una nueva lección.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $classrooms = Auth::user()->teacherClassrooms;
        
        return view('prompt_lessons.create', compact('categories', 'classrooms'));
    }

    /**
     * Almacena una nueva lección en la base de datos.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'category_id' => 'nullable|exists:categories,id',
            'estimated_time' => 'nullable|integer|min:1',
            'points' => 'nullable|integer|min:0',
            'class_id' => 'nullable|exists:classes,class_id',
            'is_public' => 'sometimes|boolean',
            'status' => 'required|in:draft,published,archived',
            'content' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $lesson = new PromptLesson();
            $lesson->title = $request->title;
            $lesson->description = $request->description;
            $lesson->content = $request->content;
            $lesson->teacher_id = Auth::id();
            $lesson->class_id = $request->class_id;
            $lesson->is_public = $request->has('is_public');
            $lesson->status = $request->status;
            $lesson->difficulty = $request->difficulty;
            $lesson->estimated_time = $request->estimated_time;
            $lesson->category_id = $request->category_id;
            $lesson->points = $request->points ?? 0;
            $lesson->save();

            // Si se enviaron ejercicios, guardarlos
            if ($request->has('exercises') && is_array($request->exercises)) {
                foreach ($request->exercises as $index => $exerciseData) {
                    $exercise = new PromptExercise();
                    $exercise->lesson_id = $lesson->id;
                    $exercise->title = $exerciseData['title'];
                    $exercise->description = $exerciseData['description'] ?? null;
                    $exercise->instructions = $exerciseData['instructions'];
                    $exercise->example_prompt = $exerciseData['example_prompt'] ?? null;
                    $exercise->hint = $exerciseData['hint'] ?? null;
                    $exercise->evaluation_criteria = $exerciseData['evaluation_criteria'] ?? null;
                    $exercise->order = $index + 1;
                    $exercise->points = $exerciseData['points'] ?? 0;
                    $exercise->save();
                }
            }

            return redirect()->route('prompt_lessons.show', $lesson->id)
                ->with('success', 'Lección creada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear lección: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ha ocurrido un error al crear la lección. Por favor, inténtalo de nuevo.')
                ->withInput();
        }
    }

    /**
     * Muestra una lección específica.
     */
    public function show($id)
    {
        $lesson = PromptLesson::with(['teacher', 'classroom', 'exercises' => function($query) {
            $query->ordered();
        }])->findOrFail($id);
        
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
                ->with('error', 'No tienes acceso a esta lección.');
        }
        
        // Si es estudiante, registrar o actualizar progreso
        if ($user->role === 'student') {
            $progress = $lesson->studentProgress()
                ->firstOrCreate(
                    ['student_id' => $user->user_id],
                    [
                        'total_exercises' => $lesson->exercises->count(),
                        'status' => 'in_progress',
                        'started_at' => now(),
                        'last_activity_at' => now()
                    ]
                );
            
            if ($progress->status === 'not_started') {
                $progress->status = 'in_progress';
                $progress->started_at = now();
                $progress->last_activity_at = now();
                $progress->save();
            } else {
                $progress->last_activity_at = now();
                $progress->save();
            }
        }
        
        return view('prompt_lessons.show', compact('lesson'));
    }

    /**
     * Muestra el formulario para editar una lección.
     */
    public function edit($id)
    {
        $lesson = PromptLesson::with(['exercises' => function($query) {
            $query->ordered();
        }])->findOrFail($id);
        
        // Verificar que el usuario sea el creador de la lección o un administrador
        if (Auth::user()->role !== 'admin' && $lesson->teacher_id !== Auth::id()) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes permiso para editar esta lección.');
        }
        
        $categories = Category::orderBy('name')->get();
        $classrooms = Auth::user()->teacherClassrooms;
        
        return view('prompt_lessons.edit', compact('lesson', 'categories', 'classrooms'));
    }

    /**
     * Actualiza la lección especificada en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $lesson = PromptLesson::findOrFail($id);
        
        // Verificar que el usuario sea el creador de la lección o un administrador
        if (Auth::user()->role !== 'admin' && $lesson->teacher_id !== Auth::id()) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes permiso para editar esta lección.');
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'category_id' => 'nullable|exists:categories,id',
            'estimated_time' => 'nullable|integer|min:1',
            'points' => 'nullable|integer|min:0',
            'class_id' => 'nullable|exists:classes,class_id',
            'is_public' => 'sometimes|boolean',
            'status' => 'required|in:draft,published,archived',
            'content' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $lesson->title = $request->title;
            $lesson->description = $request->description;
            $lesson->content = $request->content;
            $lesson->class_id = $request->class_id;
            $lesson->is_public = $request->has('is_public');
            $lesson->status = $request->status;
            $lesson->difficulty = $request->difficulty;
            $lesson->estimated_time = $request->estimated_time;
            $lesson->category_id = $request->category_id;
            $lesson->points = $request->points ?? 0;
            $lesson->save();

            // Actualizar o crear ejercicios
            if ($request->has('exercises') && is_array($request->exercises)) {
                // IDs de ejercicios actuales para control de eliminación
                $currentExerciseIds = [];
                
                foreach ($request->exercises as $index => $exerciseData) {
                    if (isset($exerciseData['id'])) {
                        // Actualizar ejercicio existente
                        $exercise = PromptExercise::findOrFail($exerciseData['id']);
                        $currentExerciseIds[] = $exercise->id;
                    } else {
                        // Crear nuevo ejercicio
                        $exercise = new PromptExercise();
                        $exercise->lesson_id = $lesson->id;
                    }
                    
                    $exercise->title = $exerciseData['title'];
                    $exercise->description = $exerciseData['description'] ?? null;
                    $exercise->instructions = $exerciseData['instructions'];
                    $exercise->example_prompt = $exerciseData['example_prompt'] ?? null;
                    $exercise->hint = $exerciseData['hint'] ?? null;
                    $exercise->evaluation_criteria = $exerciseData['evaluation_criteria'] ?? null;
                    $exercise->order = $index + 1;
                    $exercise->points = $exerciseData['points'] ?? 0;
                    $exercise->save();
                    
                    if (!isset($exerciseData['id'])) {
                        $currentExerciseIds[] = $exercise->id;
                    }
                }
                
                // Eliminar ejercicios que ya no están en la lista
                PromptExercise::where('lesson_id', $lesson->id)
                    ->whereNotIn('id', $currentExerciseIds)
                    ->delete();
            }

            return redirect()->route('prompt_lessons.show', $lesson->id)
                ->with('success', 'Lección actualizada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar lección: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ha ocurrido un error al actualizar la lección. Por favor, inténtalo de nuevo.')
                ->withInput();
        }
    }

    /**
     * Elimina la lección especificada.
     */
    public function destroy($id)
    {
        $lesson = PromptLesson::findOrFail($id);
        
        // Verificar que el usuario sea el creador de la lección o un administrador
        if (Auth::user()->role !== 'admin' && $lesson->teacher_id !== Auth::id()) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes permiso para eliminar esta lección.');
        }
        
        $lesson->delete();
        
        return redirect()->route('prompt_lessons.index')
            ->with('success', 'Lección eliminada exitosamente.');
    }

    /**
     * Muestra las estadísticas de una lección.
     */
    public function statistics($id)
    {
        $lesson = PromptLesson::with(['teacher', 'classroom'])->findOrFail($id);
        
        // Verificar que el usuario sea el creador de la lección o un administrador
        if (Auth::user()->role !== 'admin' && $lesson->teacher_id !== Auth::id()) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes permiso para ver las estadísticas de esta lección.');
        }
        
        // Obtener estadísticas
        $studentProgress = $lesson->studentProgress()
            ->with('student')
            ->get();
            
        $totalStudents = $studentProgress->count();
        $completedCount = $studentProgress->where('status', 'completed')->count();
        $inProgressCount = $studentProgress->where('status', 'in_progress')->count();
        $notStartedCount = $studentProgress->where('status', 'not_started')->count();
        
        $completionRate = $totalStudents > 0 ? round(($completedCount / $totalStudents) * 100) : 0;
        
        $averageScore = $studentProgress->where('status', 'completed')->avg('score');
        
        // Calcular calidad de los prompts (ejemplo simplificado - esto podría ser más complejo en la implementación real)
        $averagePromptQuality = $studentProgress->where('status', 'completed')->avg('score') / 100 * 5; // Escala 0-5
        
        return view('prompt_lessons.statistics', compact(
            'lesson', 
            'studentProgress', 
            'totalStudents', 
            'completedCount', 
            'inProgressCount', 
            'notStartedCount', 
            'completionRate', 
            'averageScore',
            'averagePromptQuality'
        ));
    }
} 