<?php

namespace App\Http\Controllers;

use App\Models\Hackathon;
use App\Models\HackathonRound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HackathonRoundController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:teacher,admin']);
    }
    
    /**
     * Mostrar la página de gestión de rondas
     */
    public function index($hackathonId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        
        // Verificar que el usuario es el profesor del hackathon o un administrador
        if (Auth::user()->role !== 'admin' && $hackathon->teacher_id !== Auth::id()) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar las rondas de este hackathon.');
        }
        
        // Obtener todas las rondas ordenadas por número
        $rounds = HackathonRound::where('hackathon_id', $hackathonId)
            ->orderBy('round_number')
            ->get();
            
        return view('teachers.hackathon-rounds', [
            'hackathon' => $hackathon,
            'rounds' => $rounds
        ]);
    }
    
    /**
     * Mostrar el formulario para crear una nueva ronda
     */
    public function create($hackathonId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        
        // Verificar permisos
        if (Auth::user()->role !== 'admin' && $hackathon->teacher_id !== Auth::id()) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar este hackathon.');
        }
        
        // Determinar el siguiente número de ronda
        $nextRoundNumber = HackathonRound::where('hackathon_id', $hackathonId)
            ->max('round_number') + 1;
        
        if (!$nextRoundNumber) {
            $nextRoundNumber = 1;
        }
        
        return view('teachers.hackathon-round-form', [
            'hackathon' => $hackathon,
            'round' => new HackathonRound(),
            'roundNumber' => $nextRoundNumber,
            'isEdit' => false
        ]);
    }
    
    /**
     * Almacenar una nueva ronda
     */
    public function store(Request $request, $hackathonId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        
        // Verificar permisos
        if (Auth::user()->role !== 'admin' && $hackathon->teacher_id !== Auth::id()) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar este hackathon.');
        }
        
        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'round_number' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'requirements' => 'nullable|string',
        ]);
        
        // Verificar que no exista ya una ronda con el mismo número
        $existingRound = HackathonRound::where('hackathon_id', $hackathonId)
            ->where('round_number', $validated['round_number'])
            ->exists();
            
        if ($existingRound) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe una ronda con ese número en este hackathon.');
        }
        
        // Crear la nueva ronda
        $round = new HackathonRound();
        $round->hackathon_id = $hackathonId;
        $round->name = $validated['name'];
        $round->round_number = $validated['round_number'];
        $round->start_date = $validated['start_date'];
        $round->end_date = $validated['end_date'];
        $round->description = $validated['description'];
        $round->objectives = $validated['objectives'];
        $round->requirements = $validated['requirements'];
        $round->status = 'pending';
        $round->save();
        
        // Actualizar el hackathon si es la primera ronda
        if ($round->round_number == 1 && !$hackathon->start_date) {
            $hackathon->start_date = $round->start_date;
            $hackathon->save();
        }
        
        return redirect()->route('hackathons.rounds.index', $hackathonId)
            ->with('success', 'Ronda creada correctamente.');
    }
    
    /**
     * Mostrar el formulario para editar una ronda
     */
    public function edit($hackathonId, $roundId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathonId) {
            return redirect()->route('hackathons.rounds.index', $hackathonId)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos
        if (Auth::user()->role !== 'admin' && $hackathon->teacher_id !== Auth::id()) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar este hackathon.');
        }
        
        return view('teachers.hackathon-round-form', [
            'hackathon' => $hackathon,
            'round' => $round,
            'roundNumber' => $round->round_number,
            'isEdit' => true
        ]);
    }
    
    /**
     * Actualizar una ronda
     */
    public function update(Request $request, $hackathonId, $roundId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathonId) {
            return redirect()->route('hackathons.rounds.index', $hackathonId)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos
        if (Auth::user()->role !== 'admin' && $hackathon->teacher_id !== Auth::id()) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar este hackathon.');
        }
        
        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'round_number' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'requirements' => 'nullable|string',
            'status' => 'required|in:pending,active,completed',
        ]);
        
        // Verificar que no exista ya una ronda con el mismo número (excepto esta misma)
        if ($validated['round_number'] != $round->round_number) {
            $existingRound = HackathonRound::where('hackathon_id', $hackathonId)
                ->where('round_number', $validated['round_number'])
                ->where('id', '!=', $roundId)
                ->exists();
                
            if ($existingRound) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ya existe una ronda con ese número en este hackathon.');
            }
        }
        
        // Actualizar la ronda
        $round->name = $validated['name'];
        $round->round_number = $validated['round_number'];
        $round->start_date = $validated['start_date'];
        $round->end_date = $validated['end_date'];
        $round->description = $validated['description'];
        $round->objectives = $validated['objectives'];
        $round->requirements = $validated['requirements'];
        $round->status = $validated['status'];
        $round->save();
        
        // Si el estado cambió a activo, actualizar la ronda actual del hackathon
        if ($round->status == 'active' && $hackathon->current_round != $round->round_number) {
            $hackathon->current_round = $round->round_number;
            $hackathon->save();
        }
        
        return redirect()->route('hackathons.rounds.index', $hackathonId)
            ->with('success', 'Ronda actualizada correctamente.');
    }
    
    /**
     * Eliminar una ronda
     */
    public function destroy($hackathonId, $roundId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathonId) {
            return redirect()->route('hackathons.rounds.index', $hackathonId)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos
        if (Auth::user()->role !== 'admin' && $hackathon->teacher_id !== Auth::id()) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar este hackathon.');
        }
        
        // Verificar si hay entregables asociados a esta ronda
        if ($round->deliverables()->count() > 0) {
            return redirect()->route('hackathons.rounds.index', $hackathonId)
                ->with('error', 'No puedes eliminar esta ronda porque tiene entregables asociados.');
        }
        
        // Eliminar la ronda
        $round->delete();
        
        // Si era la ronda actual del hackathon, actualizar
        if ($hackathon->current_round == $round->round_number) {
            // Obtener la última ronda por número
            $lastRound = HackathonRound::where('hackathon_id', $hackathonId)
                ->orderBy('round_number', 'desc')
                ->first();
                
            $hackathon->current_round = $lastRound ? $lastRound->round_number : null;
            $hackathon->save();
        }
        
        return redirect()->route('hackathons.rounds.index', $hackathonId)
            ->with('success', 'Ronda eliminada correctamente.');
    }
    
    /**
     * Cambiar el estado de una ronda
     */
    public function updateStatus(Request $request, $hackathonId, $roundId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathonId) {
            return redirect()->route('hackathons.rounds.index', $hackathonId)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos
        if (Auth::user()->role !== 'admin' && $hackathon->teacher_id !== Auth::id()) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar este hackathon.');
        }
        
        // Validar los datos del formulario
        $validated = $request->validate([
            'status' => 'required|in:pending,active,completed',
        ]);
        
        // Actualizar el estado de la ronda
        $round->status = $validated['status'];
        $round->save();
        
        // Si el estado cambió a activo, actualizar la ronda actual del hackathon
        if ($round->status == 'active') {
            // Primero, desactivar todas las demás rondas
            HackathonRound::where('hackathon_id', $hackathonId)
                ->where('id', '!=', $roundId)
                ->where('status', 'active')
                ->update(['status' => 'pending']);
                
            // Actualizar la ronda actual del hackathon
            $hackathon->current_round = $round->round_number;
            $hackathon->save();
        }
        
        return redirect()->route('hackathons.rounds.index', $hackathonId)
            ->with('success', 'Estado de la ronda actualizado correctamente.');
    }
} 