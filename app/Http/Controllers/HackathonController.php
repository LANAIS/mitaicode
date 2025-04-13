<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hackathon;
use App\Models\HackathonRound;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class HackathonController extends Controller
{
    // Constructor sin middleware
    public function __construct()
    {
        // El middleware auth se aplicará a través de las rutas
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Hackathon::query();

        // Filtrar por estado si se especifica
        if ($request->has('filter')) {
            $filter = $request->input('filter');
            if ($filter === 'active') {
                $query->where('status', 'active');
            } elseif ($filter === 'finished') {
                $query->where('status', 'finished');
            } elseif ($filter === 'pending') {
                $query->where('status', 'pending');
            }
        }

        // Si es profesor, mostrar también sus hackathones
        if (Auth::user()->role === 'teacher') {
            $query->where(function($q) {
                $q->where('status', 'active')
                  ->orWhere('created_by', Auth::id());
            });
        } else {
            // Para estudiantes, solo mostrar hackathones activos
            $query->where('status', 'active');
        }

        // Cargar el recuento de equipos, participantes y rondas
        $hackathons = $query->withCount(['teams', 'rounds'])->orderBy('created_at', 'desc')->paginate(10);

        return view('hackathons.index', compact('hackathons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Solo profesores y admins pueden crear hackathones - permitir a todos los profesores
        if (Auth::user()->role === 'teacher' || Auth::user()->role === 'admin') {
            return view('hackathons.create');
        }
        
        return redirect()->route('hackathons.index')
            ->with('error', 'No tienes permiso para crear hackathones.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Solo profesores y admins pueden crear hackathones - permitir a todos los profesores
        if (Auth::user()->role !== 'teacher' && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para crear hackathones.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'max_participants' => 'required|integer|min:5|max:500',
            'max_teams' => 'required|integer|min:1|max:100',
            'team_size' => 'required|integer|min:1|max:10',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'rounds' => 'nullable|array',
            'rounds.*.name' => 'required|string|max:255',
            'rounds.*.description' => 'nullable|string',
            'rounds.*.start_date' => 'required|date',
            'rounds.*.end_date' => 'required|date|after_or_equal:rounds.*.start_date',
            'rounds.*.objectives' => 'nullable|string',
            'rounds.*.deliverables' => 'nullable|string',
        ]);
        
        // Iniciar transacción para garantizar que se creen tanto el hackathon como sus rondas
        DB::beginTransaction();
        
        try {
            $hackathon = new Hackathon();
            $hackathon->title = $request->title;
            $hackathon->description = $request->description;
            $hackathon->created_by = Auth::id();
            $hackathon->max_participants = $request->max_participants;
            $hackathon->max_teams = $request->max_teams;
            $hackathon->team_size = $request->team_size;
            $hackathon->start_date = $request->start_date;
            $hackathon->end_date = $request->end_date;
            $hackathon->status = 'pending';
            
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('hackathons', 'public');
                $hackathon->image = $path;
            }
            
            $hackathon->save();
            
            // Procesar rondas si se proporcionaron
            if ($request->has('rounds') && is_array($request->rounds)) {
                foreach ($request->rounds as $index => $roundData) {
                    $round = new HackathonRound();
                    $round->hackathon_id = $hackathon->hackathon_id;
                    $round->name = $roundData['name'];
                    $round->description = $roundData['description'] ?? '';
                    $round->start_date = $roundData['start_date'];
                    $round->end_date = $roundData['end_date'];
                    $round->objectives = $roundData['objectives'] ?? '';
                    $round->deliverables = $roundData['deliverables'] ?? '';
                    $round->order = $index + 1;
                    $round->is_active = ($index === 0); // La primera ronda está activa por defecto
                    $round->save();
                }
            }
            
            DB::commit();
            
            // Redireccionar a la lista de hackathons con mensaje de éxito
            return redirect()->route('hackathons.index')
                ->with('success', 'Hackathon "' . $hackathon->title . '" creado correctamente con ' . count($request->rounds ?? []) . ' rondas');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'Error al crear el hackathon: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hackathon = Hackathon::with(['rounds' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($id);
        
        // Obtener información adicional para mostrar en la vista
        $isUserJudge = false; // Por implementar
        $userTeam = null; // Por implementar: buscar el equipo del usuario actual
        
        return view('hackathons.show', compact('hackathon', 'isUserJudge', 'userTeam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $hackathon = Hackathon::with(['rounds' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($id);
        
        // Verificar permisos - corregido para utilizar el ID correcto
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para editar este hackathon.');
        }
        
        return view('hackathons.edit', compact('hackathon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar permisos - corregido para utilizar el ID correcto
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para editar este hackathon.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'max_participants' => 'required|integer|min:5|max:500',
            'max_teams' => 'required|integer|min:1|max:100',
            'team_size' => 'required|integer|min:1|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:pending,active,finished',
        ]);
        
        $hackathon->title = $request->title;
        $hackathon->description = $request->description;
        $hackathon->max_participants = $request->max_participants;
        $hackathon->max_teams = $request->max_teams;
        $hackathon->team_size = $request->team_size;
        $hackathon->start_date = $request->start_date;
        $hackathon->end_date = $request->end_date;
        $hackathon->status = $request->status;
        
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($hackathon->image) {
                Storage::disk('public')->delete($hackathon->image);
            }
            $path = $request->file('image')->store('hackathons', 'public');
            $hackathon->image = $path;
        }
        
        $hackathon->save();
        
        return redirect()->route('hackathons.show', $hackathon->hackathon_id)
            ->with('success', 'Hackathon actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar permisos - corregido
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para eliminar este hackathon.');
        }
        
        // Eliminar imagen si existe
        if ($hackathon->image) {
            Storage::disk('public')->delete($hackathon->image);
        }
        
        $hackathon->delete();
        
        return redirect()->route('hackathons.index')
            ->with('success', 'Hackathon eliminado correctamente.');
    }
    
    /**
     * Mostrar el formulario para gestionar jurados
     */
    public function judges($id)
    {
        $hackathon = Hackathon::with('judges')->findOrFail($id);
        
        // Verificar permisos
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar jurados en este hackathon.');
        }
        
        // Obtener usuarios que pueden ser jurados (profesores y administradores que no son ya jurados)
        $potentialJudges = User::whereIn('role', ['teacher', 'admin'])
            ->whereNotIn('user_id', $hackathon->judges->pluck('user_id')->toArray())
            ->get();
        
        $currentJudges = $hackathon->judges;
        
        return view('hackathons.judges', compact('hackathon', 'potentialJudges', 'currentJudges'));
    }
    
    /**
     * Actualizar los jurados del hackathon
     */
    public function updateJudges(Request $request, $id)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar permisos
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar jurados en este hackathon.');
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'is_lead_judge' => 'boolean',
            'notes' => 'nullable|string|max:255',
        ]);
        
        try {
            // Añadir jurado al hackathon
            $hackathon->judges()->attach($request->user_id, [
                'is_lead_judge' => $request->boolean('is_lead_judge'),
                'notes' => $request->notes,
            ]);
            
            return redirect()->route('hackathons.judges', $hackathon->id)
                ->with('success', 'Jurado añadido correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('hackathons.judges', $hackathon->id)
                ->with('error', 'Error al añadir jurado: ' . $e->getMessage());
        }
    }
    
    /**
     * Eliminar un jurado del hackathon
     */
    public function removeJudge($id, $userId)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar permisos
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para eliminar jurados de este hackathon.');
        }
        
        try {
            $hackathon->judges()->detach($userId);
            
            return redirect()->route('hackathons.judges', $hackathon->id)
                ->with('success', 'Jurado eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('hackathons.judges', $hackathon->id)
                ->with('error', 'Error al eliminar jurado: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar la vista de gestión de rondas del hackathon
     */
    public function rounds(string $id)
    {
        $hackathon = Hackathon::with(['rounds' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($id);
        
        // Verificar permisos - corregido
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar las rondas de este hackathon.');
        }
        
        // Redirigir a la nueva ruta de gestión de rondas
        return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id);
    }
    
    /**
     * Crear una nueva ronda para el hackathon
     */
    public function createRound(Request $request, string $id)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar permisos - corregido
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para añadir rondas a este hackathon.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:' . $hackathon->start_date,
            'end_date' => 'required|date|before_or_equal:' . $hackathon->end_date . '|after_or_equal:start_date',
            'objectives' => 'nullable|string',
            'deliverables' => 'nullable|string',
        ]);
        
        // Obtener el último orden
        $lastOrder = $hackathon->rounds()->max('order') ?? 0;
        
        // Crear la nueva ronda
        $round = new HackathonRound();
        $round->hackathon_id = $hackathon->id;
        $round->name = $request->name;
        $round->description = $request->description;
        $round->start_date = $request->start_date;
        $round->end_date = $request->end_date;
        $round->objectives = $request->objectives;
        $round->deliverables = $request->deliverables;
        $round->order = $lastOrder + 1;
        $round->is_active = false; // No activar automáticamente
        $round->save();
        
        return redirect()->route('hackathons.rounds', $hackathon->id)
            ->with('success', 'Ronda creada correctamente.');
    }
    
    /**
     * Actualizar una ronda existente
     */
    public function updateRound(Request $request, string $id, string $roundId)
    {
        $hackathon = Hackathon::findOrFail($id);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathon->id) {
            return redirect()->route('hackathons.rounds', $hackathon->id)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos - corregido
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para editar las rondas de este hackathon.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:' . $hackathon->start_date,
            'end_date' => 'required|date|before_or_equal:' . $hackathon->end_date . '|after_or_equal:start_date',
            'objectives' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Actualizar la ronda
        $round->name = $request->name;
        $round->description = $request->description;
        $round->start_date = $request->start_date;
        $round->end_date = $request->end_date;
        $round->objectives = $request->objectives;
        $round->deliverables = $request->deliverables;
        
        // Si se está activando esta ronda, desactivar las demás
        if ($request->is_active && !$round->is_active) {
            $hackathon->rounds()->where('id', '!=', $round->id)->update(['is_active' => false]);
            $round->is_active = true;
        } elseif (!$request->is_active && $round->is_active) {
            // No permitir desactivar si es la única ronda activa
            $activeRoundsCount = $hackathon->rounds()->where('is_active', true)->count();
            if ($activeRoundsCount <= 1) {
                return redirect()->route('hackathons.rounds', $hackathon->id)
                    ->with('error', 'Debe haber al menos una ronda activa.');
            }
            $round->is_active = false;
        }
        
        $round->save();
        
        return redirect()->route('hackathons.rounds', $hackathon->id)
            ->with('success', 'Ronda actualizada correctamente.');
    }
    
    /**
     * Eliminar una ronda
     */
    public function destroyRound(string $id, string $roundId)
    {
        $hackathon = Hackathon::findOrFail($id);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathon->id) {
            return redirect()->route('hackathons.rounds', $hackathon->id)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos - corregido
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para eliminar rondas de este hackathon.');
        }
        
        // No permitir eliminar si es la única ronda
        $roundsCount = $hackathon->rounds()->count();
        if ($roundsCount <= 1) {
            return redirect()->route('hackathons.rounds', $hackathon->id)
                ->with('error', 'No se puede eliminar la única ronda del hackathon.');
        }
        
        // No permitir eliminar si es la única ronda activa
        if ($round->is_active && $hackathon->rounds()->where('is_active', true)->count() <= 1) {
            return redirect()->route('hackathons.rounds', $hackathon->id)
                ->with('error', 'No se puede eliminar la única ronda activa.');
        }
        
        // Antes de eliminar, ajustar el orden de las demás rondas
        HackathonRound::where('hackathon_id', $hackathon->id)
            ->where('order', '>', $round->order)
            ->decrement('order');
        
        $round->delete();
        
        return redirect()->route('hackathons.rounds', $hackathon->id)
            ->with('success', 'Ronda eliminada correctamente.');
    }
    
    /**
     * Reordenar las rondas
     */
    public function reorderRounds(Request $request, string $id)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar permisos - corregido
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return response()->json(['error' => 'No tienes permiso para reordenar las rondas de este hackathon.'], 403);
        }
        
        $request->validate([
            'rounds' => 'required|array',
            'rounds.*' => 'exists:hackathon_rounds,id',
        ]);
        
        // Actualizar el orden de las rondas
        foreach ($request->rounds as $index => $roundId) {
            HackathonRound::where('id', $roundId)
                ->where('hackathon_id', $hackathon->id)
                ->update(['order' => $index + 1]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Abre o cierra las inscripciones para un hackathon
     */
    public function toggleRegistration(string $id)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar permisos - corregido
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('hackathons.show', $hackathon->hackathon_id)
                ->with('error', 'No tienes permiso para cambiar el estado de este hackathon.');
        }
        
        // Cambiar el estado
        if ($hackathon->status === 'pending') {
            $hackathon->status = 'active';
            $message = 'Inscripciones abiertas correctamente.';
        } else if ($hackathon->status === 'active') {
            $hackathon->status = 'closed';
            $message = 'Inscripciones cerradas correctamente.';
        } else if ($hackathon->status === 'closed') {
            $hackathon->status = 'active';
            $message = 'Inscripciones reabiertas correctamente.';
        } else {
            return redirect()->route('hackathons.show', $hackathon->hackathon_id)
                ->with('error', 'No se puede cambiar el estado del hackathon en su estado actual.');
        }
        
        $hackathon->save();
        
        return redirect()->route('hackathons.show', $hackathon->hackathon_id)
            ->with('success', $message);
    }
    
    /**
     * Cambia el estado del hackathon
     */
    public function updateStatus(string $id, Request $request)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar permisos - corregido
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('hackathons.show', $hackathon->hackathon_id)
                ->with('error', 'No tienes permiso para cambiar el estado de este hackathon.');
        }
        
        $newStatus = $request->input('status');
        
        // Validar que el nuevo estado sea válido
        if (!in_array($newStatus, ['pending', 'active', 'finished'])) {
            return redirect()->route('hackathons.show', $hackathon->hackathon_id)
                ->with('error', 'Estado no válido.');
        }
        
        // Cambiar el estado
        $hackathon->status = $newStatus;
        $hackathon->save();
        
        // Mensaje personalizado según el estado
        $statusMessages = [
            'pending' => 'El hackathon ha sido marcado como pendiente.',
            'active' => 'El hackathon ha sido activado correctamente.',
            'finished' => 'El hackathon ha sido finalizado correctamente.'
        ];
        
        return redirect()->route('hackathons.show', $hackathon->hackathon_id)
            ->with('success', $statusMessages[$newStatus]);
    }
    
    /**
     * Avanza a la siguiente ronda del hackathon
     */
    public function advanceRound(string $id)
    {
        $hackathon = Hackathon::with(['rounds' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($id);
        
        // Verificar permisos - corregido
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('hackathons.show', $hackathon->hackathon_id)
                ->with('error', 'No tienes permiso para cambiar la ronda de este hackathon.');
        }
        
        // Verificar que hay rondas
        if ($hackathon->rounds->count() === 0) {
            return redirect()->route('hackathons.show', $hackathon->hackathon_id)
                ->with('error', 'No hay rondas configuradas para este hackathon.');
        }
        
        // Encontrar la ronda activa actual
        $currentActiveRound = $hackathon->rounds->where('is_active', true)->first();
        
        if (!$currentActiveRound) {
            // Si no hay ronda activa, activar la primera
            $newActiveRound = $hackathon->rounds->sortBy('order')->first();
            $newActiveRound->is_active = true;
            $newActiveRound->save();
            
            return redirect()->route('hackathons.show', $hackathon->hackathon_id)
                ->with('success', 'Primera ronda activada correctamente.');
        }
        
        // Encontrar la siguiente ronda por orden
        $nextRound = $hackathon->rounds->where('order', '>', $currentActiveRound->order)
                                      ->sortBy('order')
                                      ->first();
        
        if (!$nextRound) {
            return redirect()->route('hackathons.show', $hackathon->hackathon_id)
                ->with('error', 'Ya estás en la última ronda del hackathon.');
        }
        
        // Desactivar la ronda actual
        $currentActiveRound->is_active = false;
        $currentActiveRound->save();
        
        // Activar la siguiente ronda
        $nextRound->is_active = true;
        $nextRound->save();
        
        return redirect()->route('hackathons.show', $hackathon->hackathon_id)
            ->with('success', 'Avanzado a la ronda: ' . $nextRound->name);
    }

    /**
     * Display a listing of hackathons where the teacher is creator or judge.
     *
     * @return \Illuminate\View\View
     */
    public function teacherIndex(Request $request)
    {
        // Verificar permisos
        if (Auth::user()->role !== 'teacher' && Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección.');
        }
        
        // Obtener hackathones donde el profesor es creador
        $createdQuery = Hackathon::where('created_by', Auth::id());
        
        // Obtener hackathones donde el profesor es juez
        $judgedQuery = Hackathon::whereHas('judges', function($query) {
                $query->where('hackathon_judges.user_id', Auth::id());
            });
            
        // Filtrar por estado si se especifica
        if ($request->has('status') && $request->status) {
            $createdQuery->where('status', $request->status);
            $judgedQuery->where('status', $request->status);
        }
        
        // Ejecutar consultas con relaciones
        $createdHackathons = collect([]);
        $judgedHackathons = collect([]);
        
        // Filtrar por rol si se especifica
        if (!$request->has('role') || !$request->role || $request->role == 'creator') {
            $createdHackathons = $createdQuery
                ->with(['rounds', 'teams', 'teams.deliverables'])
                ->withCount(['teams', 'rounds'])
                ->get();
        }
        
        if (!$request->has('role') || !$request->role || $request->role == 'judge') {
            $judgedHackathons = $judgedQuery
                ->with(['rounds', 'teams', 'teams.deliverables'])
                ->withCount(['teams', 'rounds'])
                ->get();
        }
        
        // Combinar y eliminar duplicados (por si es creador y juez al mismo tiempo)
        $hackathons = $createdHackathons->merge($judgedHackathons)->unique('id');
        
        // Obtener entregables pendientes de evaluación para cada hackathon
        foreach ($hackathons as $key => $hackathon) {
            // Contador de entregables pendientes
            $pendingDeliverables = 0;
            
            foreach ($hackathon->teams as $team) {
                $pendingDeliverables += $team->deliverables()
                    ->whereNull('evaluated_at')
                    ->count();
            }
            
            $hackathon->pending_deliverables_count = $pendingDeliverables;
            
            // Filtrar por entregables pendientes si se especifica
            if ($request->has('pending') && $request->pending == 'pending' && $pendingDeliverables == 0) {
                $hackathons->forget($key);
                continue;
            }
            
            // Obtener la ronda activa
            $hackathon->active_round = $hackathon->rounds()
                ->where('is_active', true)
                ->first();
        }
        
        return view('teachers.hackathons', compact('hackathons'));
    }
} 