<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Classroom;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $classrooms = [];
        
        if ($user->role === 'teacher') {
            // Si es profesor, mostrar sus aulas
            $classrooms = Classroom::where('teacher_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->role === 'admin') {
            // Si es admin, mostrar todas las aulas
            $classrooms = Classroom::orderBy('created_at', 'desc')->get();
        }
        
        return view('classrooms.index', compact('classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('classrooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'access_code' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $classroom = new Classroom();
        $classroom->name = $validated['name'];
        $classroom->description = $validated['description'] ?? '';
        $classroom->access_code = $validated['access_code'] ?? substr(md5(uniqid(mt_rand(), true)), 0, 8);
        $classroom->is_active = $validated['is_active'] ?? true;
        $classroom->teacher_id = Auth::id();
        $classroom->save();
        
        return redirect()->route('classrooms.show', $classroom->class_id)
            ->with('success', 'Aula creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $classroom = Classroom::findOrFail($id);
        
        // Verificar que el usuario sea el profesor del aula o un administrador
        if (Auth::user()->role !== 'admin' && $classroom->teacher_id !== Auth::id()) {
            return redirect()->route('classrooms.index')
                ->with('error', 'No tienes permiso para ver esta aula.');
        }
        
        return view('classrooms.show', compact('classroom'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $classroom = Classroom::findOrFail($id);
        
        // Verificar que el usuario sea el profesor del aula o un administrador
        if (Auth::user()->role !== 'admin' && $classroom->teacher_id !== Auth::id()) {
            return redirect()->route('classrooms.index')
                ->with('error', 'No tienes permiso para editar esta aula.');
        }
        
        return view('classrooms.edit', compact('classroom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $classroom = Classroom::findOrFail($id);
        
        // Verificar que el usuario sea el profesor del aula o un administrador
        if (Auth::user()->role !== 'admin' && $classroom->teacher_id !== Auth::id()) {
            return redirect()->route('classrooms.index')
                ->with('error', 'No tienes permiso para editar esta aula.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'access_code' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $classroom->name = $validated['name'];
        $classroom->description = $validated['description'] ?? $classroom->description;
        $classroom->access_code = $validated['access_code'] ?? $classroom->access_code;
        $classroom->is_active = $validated['is_active'] ?? $classroom->is_active;
        $classroom->save();
        
        return redirect()->route('classrooms.show', $classroom->class_id)
            ->with('success', 'Aula actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $classroom = Classroom::findOrFail($id);
        
        // Verificar que el usuario sea el profesor del aula o un administrador
        if (Auth::user()->role !== 'admin' && $classroom->teacher_id !== Auth::id()) {
            return redirect()->route('classrooms.index')
                ->with('error', 'No tienes permiso para eliminar esta aula.');
        }
        
        $classroom->delete();
        
        return redirect()->route('classrooms.index')
            ->with('success', 'Aula eliminada exitosamente.');
    }
}
