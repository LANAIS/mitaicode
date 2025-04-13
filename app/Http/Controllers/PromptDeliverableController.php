<?php

namespace App\Http\Controllers;

use App\Models\PromptDeliverable;
use App\Models\PromptLesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PromptDeliverableController extends Controller
{
    /**
     * Muestra todos los entregables de una lección para un profesor.
     */
    public function index($lessonId)
    {
        $lesson = PromptLesson::with('teacher')->findOrFail($lessonId);
        $user = Auth::user();
        
        // Verificar que el usuario sea el profesor de la lección o un administrador
        if ($user->role !== 'admin' && $lesson->teacher_id !== $user->user_id) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes permiso para ver los entregables de esta lección.');
        }
        
        $deliverables = PromptDeliverable::where('lesson_id', $lessonId)
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        // Agrupar por estado para visualización
        $pendingDeliverables = $deliverables->where('status', 'submitted');
        $gradedDeliverables = $deliverables->whereIn('status', ['graded', 'returned']);
        $draftDeliverables = $deliverables->where('status', 'draft');
        
        return view('prompt_deliverables.index', compact(
            'lesson', 
            'pendingDeliverables', 
            'gradedDeliverables', 
            'draftDeliverables'
        ));
    }
    
    /**
     * Muestra los entregables de un estudiante para el profesor.
     */
    public function studentDeliverables($lessonId, $studentId)
    {
        $lesson = PromptLesson::with('teacher')->findOrFail($lessonId);
        $student = User::findOrFail($studentId);
        $user = Auth::user();
        
        // Verificar permisos
        if ($user->role !== 'admin' && $lesson->teacher_id !== $user->user_id) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes permiso para ver los entregables de este estudiante.');
        }
        
        $deliverables = PromptDeliverable::where('lesson_id', $lessonId)
            ->where('student_id', $studentId)
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        return view('prompt_deliverables.student', compact('lesson', 'student', 'deliverables'));
    }
    
    /**
     * Muestra los entregables del estudiante actualmente autenticado.
     */
    public function myDeliverables($lessonId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return redirect()->route('prompt_lessons.show', $lessonId)
                ->with('error', 'Solo los estudiantes pueden ver sus entregables.');
        }
        
        $lesson = PromptLesson::findOrFail($lessonId);
        $deliverables = PromptDeliverable::where('lesson_id', $lessonId)
            ->where('student_id', $user->user_id)
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('prompt_deliverables.my_deliverables', compact('lesson', 'deliverables'));
    }
    
    /**
     * Muestra el formulario para crear un nuevo entregable.
     */
    public function create($lessonId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return redirect()->route('prompt_lessons.show', $lessonId)
                ->with('error', 'Solo los estudiantes pueden crear entregables.');
        }
        
        $lesson = PromptLesson::findOrFail($lessonId);
        
        // Verificar acceso a la lección
        $canAccess = false;
        if ($lesson->is_public) {
            $canAccess = true;
        } elseif ($lesson->class_id && $user->studentEnrollments->contains('class_id', $lesson->class_id)) {
            $canAccess = true;
        }
        
        if (!$canAccess) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes acceso a esta lección.');
        }
        
        return view('prompt_deliverables.create', compact('lesson'));
    }
    
    /**
     * Almacena un nuevo entregable en la base de datos.
     */
    public function store(Request $request, $lessonId)
    {
        $user = Auth::user();
        
        if ($user->role !== 'student') {
            return redirect()->route('prompt_lessons.show', $lessonId)
                ->with('error', 'Solo los estudiantes pueden crear entregables.');
        }
        
        $lesson = PromptLesson::findOrFail($lessonId);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // Máximo 10MB
            'status' => 'required|in:draft,submitted',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $deliverable = new PromptDeliverable();
            $deliverable->lesson_id = $lessonId;
            $deliverable->student_id = $user->user_id;
            $deliverable->title = $request->title;
            $deliverable->description = $request->description;
            $deliverable->content = $request->content;
            $deliverable->status = $request->status;
            
            if ($request->status === 'submitted') {
                $deliverable->submitted_at = now();
            }
            
            // Procesar archivo si se proporcionó
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('deliverables', 'public');
                $deliverable->file_path = $path;
            }
            
            $deliverable->save();
            
            $message = $request->status === 'submitted' ? 
                'Entregable enviado exitosamente para evaluación.' : 
                'Entregable guardado como borrador.';
                
            return redirect()->route('prompt_deliverables.my_deliverables', $lessonId)
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error al crear entregable: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ha ocurrido un error al guardar el entregable. Por favor, inténtalo de nuevo.')
                ->withInput();
        }
    }
    
    /**
     * Muestra un entregable específico.
     */
    public function show($deliverableId)
    {
        $deliverable = PromptDeliverable::with(['lesson', 'student'])->findOrFail($deliverableId);
        $user = Auth::user();
        
        // Verificar permisos para ver este entregable
        $canAccess = false;
        
        if ($user->role === 'admin' || $user->user_id === $deliverable->lesson->teacher_id) {
            $canAccess = true;
        } elseif ($user->user_id === $deliverable->student_id) {
            $canAccess = true;
        }
        
        if (!$canAccess) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes permiso para ver este entregable.');
        }
        
        return view('prompt_deliverables.show', compact('deliverable'));
    }
    
    /**
     * Muestra el formulario para editar un entregable existente.
     */
    public function edit($deliverableId)
    {
        $deliverable = PromptDeliverable::with('lesson')->findOrFail($deliverableId);
        $user = Auth::user();
        
        // Solo el estudiante propietario puede editar sus propios entregables no evaluados
        if ($user->user_id !== $deliverable->student_id) {
            return redirect()->route('prompt_lessons.show', $deliverable->lesson_id)
                ->with('error', 'No puedes editar este entregable.');
        }
        
        // No se pueden editar entregables ya calificados
        if ($deliverable->status === 'graded' || $deliverable->status === 'returned') {
            return redirect()->route('prompt_deliverables.show', $deliverableId)
                ->with('error', 'No puedes editar un entregable que ya ha sido calificado.');
        }
        
        return view('prompt_deliverables.edit', compact('deliverable'));
    }
    
    /**
     * Actualiza un entregable existente en la base de datos.
     */
    public function update(Request $request, $deliverableId)
    {
        $deliverable = PromptDeliverable::findOrFail($deliverableId);
        $user = Auth::user();
        
        // Solo el estudiante propietario puede actualizar sus propios entregables no evaluados
        if ($user->user_id !== $deliverable->student_id) {
            return redirect()->route('prompt_lessons.show', $deliverable->lesson_id)
                ->with('error', 'No puedes actualizar este entregable.');
        }
        
        // No se pueden actualizar entregables ya calificados
        if ($deliverable->status === 'graded' || $deliverable->status === 'returned') {
            return redirect()->route('prompt_deliverables.show', $deliverableId)
                ->with('error', 'No puedes actualizar un entregable que ya ha sido calificado.');
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // Máximo 10MB
            'status' => 'required|in:draft,submitted',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $deliverable->title = $request->title;
            $deliverable->description = $request->description;
            $deliverable->content = $request->content;
            
            // Actualizar estado y fecha de envío si cambia a "submitted"
            if ($deliverable->status === 'draft' && $request->status === 'submitted') {
                $deliverable->submitted_at = now();
            }
            $deliverable->status = $request->status;
            
            // Procesar archivo si se proporcionó uno nuevo
            if ($request->hasFile('file')) {
                // Eliminar archivo anterior si existe
                if ($deliverable->file_path) {
                    Storage::disk('public')->delete($deliverable->file_path);
                }
                
                $file = $request->file('file');
                $path = $file->store('deliverables', 'public');
                $deliverable->file_path = $path;
            }
            
            $deliverable->save();
            
            $message = $request->status === 'submitted' ? 
                'Entregable enviado exitosamente para evaluación.' : 
                'Entregable guardado como borrador.';
                
            return redirect()->route('prompt_deliverables.my_deliverables', $deliverable->lesson_id)
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error al actualizar entregable: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ha ocurrido un error al actualizar el entregable. Por favor, inténtalo de nuevo.')
                ->withInput();
        }
    }
    
    /**
     * Elimina un entregable existente.
     */
    public function destroy($deliverableId)
    {
        $deliverable = PromptDeliverable::findOrFail($deliverableId);
        $user = Auth::user();
        
        // Verificar permisos: solo el propietario puede borrar borradores, o el profesor/admin puede borrar cualquiera
        $canDelete = false;
        
        if ($user->role === 'admin' || $user->user_id === $deliverable->lesson->teacher_id) {
            $canDelete = true;
        } elseif ($user->user_id === $deliverable->student_id && $deliverable->status === 'draft') {
            $canDelete = true;
        }
        
        if (!$canDelete) {
            return redirect()->route('prompt_deliverables.show', $deliverableId)
                ->with('error', 'No tienes permiso para eliminar este entregable.');
        }
        
        try {
            // Eliminar archivo si existe
            if ($deliverable->file_path) {
                Storage::disk('public')->delete($deliverable->file_path);
            }
            
            $lessonId = $deliverable->lesson_id;
            $deliverable->delete();
            
            if ($user->role === 'student') {
                return redirect()->route('prompt_deliverables.my_deliverables', $lessonId)
                    ->with('success', 'Entregable eliminado exitosamente.');
            } else {
                return redirect()->route('prompt_deliverables.index', $lessonId)
                    ->with('success', 'Entregable eliminado exitosamente.');
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar entregable: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ha ocurrido un error al eliminar el entregable. Por favor, inténtalo de nuevo.');
        }
    }
    
    /**
     * Muestra el formulario para calificar un entregable.
     */
    public function grade($deliverableId)
    {
        $deliverable = PromptDeliverable::with(['lesson', 'student'])->findOrFail($deliverableId);
        $user = Auth::user();
        
        // Solo el profesor de la lección o un administrador pueden calificar
        if ($user->role !== 'admin' && $user->user_id !== $deliverable->lesson->teacher_id) {
            return redirect()->route('prompt_lessons.show', $deliverable->lesson_id)
                ->with('error', 'No tienes permiso para calificar este entregable.');
        }
        
        // Solo se pueden calificar entregables enviados
        if ($deliverable->status !== 'submitted') {
            return redirect()->route('prompt_deliverables.show', $deliverableId)
                ->with('error', 'Este entregable no está listo para ser calificado.');
        }
        
        return view('prompt_deliverables.grade', compact('deliverable'));
    }
    
    /**
     * Guarda la calificación de un entregable.
     */
    public function submitGrade(Request $request, $deliverableId)
    {
        $deliverable = PromptDeliverable::with('lesson')->findOrFail($deliverableId);
        $user = Auth::user();
        
        // Solo el profesor de la lección o un administrador pueden calificar
        if ($user->role !== 'admin' && $user->user_id !== $deliverable->lesson->teacher_id) {
            return redirect()->route('prompt_lessons.show', $deliverable->lesson_id)
                ->with('error', 'No tienes permiso para calificar este entregable.');
        }
        
        // Solo se pueden calificar entregables enviados
        if ($deliverable->status !== 'submitted') {
            return redirect()->route('prompt_deliverables.show', $deliverableId)
                ->with('error', 'Este entregable no está listo para ser calificado.');
        }
        
        $validator = Validator::make($request->all(), [
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'required|string',
            'status' => 'required|in:graded,returned',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $deliverable->grade = $request->grade;
            $deliverable->feedback = $request->feedback;
            $deliverable->status = $request->status;
            $deliverable->graded_at = now();
            $deliverable->save();
            
            return redirect()->route('prompt_deliverables.show', $deliverableId)
                ->with('success', 'Entregable calificado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al calificar entregable: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ha ocurrido un error al calificar el entregable. Por favor, inténtalo de nuevo.')
                ->withInput();
        }
    }
    
    /**
     * Descarga el archivo de un entregable.
     */
    public function download($deliverableId)
    {
        $deliverable = PromptDeliverable::with(['lesson', 'student'])->findOrFail($deliverableId);
        $user = Auth::user();
        
        // Verificar permisos para descargar
        $canAccess = false;
        
        if ($user->role === 'admin' || $user->user_id === $deliverable->lesson->teacher_id) {
            $canAccess = true;
        } elseif ($user->user_id === $deliverable->student_id) {
            $canAccess = true;
        }
        
        if (!$canAccess) {
            return redirect()->route('prompt_lessons.index')
                ->with('error', 'No tienes permiso para descargar este archivo.');
        }
        
        if (!$deliverable->file_path) {
            return redirect()->back()
                ->with('error', 'Este entregable no tiene ningún archivo adjunto.');
        }
        
        try {
            $path = Storage::disk('public')->path($deliverable->file_path);
            $fileName = basename($path);
            
            return response()->download($path, $fileName);
        } catch (\Exception $e) {
            Log::error('Error al descargar archivo: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ha ocurrido un error al descargar el archivo. Por favor, inténtalo de nuevo.');
        }
    }
}
