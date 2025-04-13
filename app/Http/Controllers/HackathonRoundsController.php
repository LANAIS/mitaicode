<?php

namespace App\Http\Controllers;

use App\Models\Hackathon;
use App\Models\HackathonRound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HackathonRoundsController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // El middleware auth y role se aplicará a través de las rutas
    }

    /**
     * Mostrar la vista de gestión de rondas del hackathon
     */
    public function index(string $hackathonId)
    {
        $hackathon = Hackathon::with(['rounds' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($hackathonId);
        
        // Verificar permisos
        if (Auth::id() !== $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar las rondas de este hackathon.');
        }
        
        return view('hackathons.rounds', compact('hackathon'));
    }
    
    /**
     * Crear una nueva ronda para el hackathon
     */
    public function store(Request $request, string $hackathonId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        
        // Verificar permisos
        if (Auth::id() !== $hackathon->created_by && Auth::user()->role !== 'admin') {
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
        $round->hackathon_id = $hackathon->hackathon_id;
        $round->name = $request->name;
        $round->description = $request->description;
        $round->start_date = $request->start_date;
        $round->end_date = $request->end_date;
        $round->objectives = $request->objectives;
        $round->deliverables = $request->deliverables;
        $round->order = $lastOrder + 1;
        $round->is_active = false; // No activar automáticamente
        $round->save();
        
        return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
            ->with('success', 'Ronda creada correctamente.');
    }
    
    /**
     * Mostrar el formulario para crear una nueva ronda
     */
    public function create(string $hackathonId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        
        // Verificar permisos
        if (Auth::id() !== $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para añadir rondas a este hackathon.');
        }
        
        return view('hackathons.rounds-create', compact('hackathon'));
    }
    
    /**
     * Mostrar el formulario para editar una ronda
     */
    public function edit(string $hackathonId, string $roundId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathon->hackathon_id) {
            return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos
        if (Auth::id() !== $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para editar las rondas de este hackathon.');
        }
        
        return view('hackathons.rounds-edit', compact('hackathon', 'round'));
    }
    
    /**
     * Actualizar una ronda existente
     */
    public function update(Request $request, string $hackathonId, string $roundId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathon->hackathon_id) {
            return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos
        if (Auth::id() !== $hackathon->created_by && Auth::user()->role !== 'admin') {
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
        if ($request->has('is_active') && $request->is_active && !$round->is_active) {
            $hackathon->rounds()->where('round_id', '!=', $round->round_id)->update(['is_active' => false]);
            $round->is_active = true;
        } elseif ($request->has('is_active') && !$request->is_active && $round->is_active) {
            // No permitir desactivar si es la única ronda activa
            $activeRoundsCount = $hackathon->rounds()->where('is_active', true)->count();
            if ($activeRoundsCount <= 1) {
                return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                    ->with('error', 'Debe haber al menos una ronda activa.');
            }
            $round->is_active = false;
        }
        
        $round->save();
        
        return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
            ->with('success', 'Ronda actualizada correctamente.');
    }
    
    /**
     * Eliminar una ronda
     */
    public function destroy(string $hackathonId, string $roundId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathon->hackathon_id) {
            return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos
        if (Auth::id() !== $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para eliminar rondas de este hackathon.');
        }
        
        // No permitir eliminar si es la única ronda
        $roundsCount = $hackathon->rounds()->count();
        if ($roundsCount <= 1) {
            return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                ->with('error', 'No se puede eliminar la única ronda del hackathon.');
        }
        
        // No permitir eliminar si es la única ronda activa
        if ($round->is_active && $hackathon->rounds()->where('is_active', true)->count() <= 1) {
            return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                ->with('error', 'No se puede eliminar la única ronda activa.');
        }
        
        // Antes de eliminar, ajustar el orden de las demás rondas
        HackathonRound::where('hackathon_id', $hackathon->hackathon_id)
            ->where('order', '>', $round->order)
            ->decrement('order');
        
        $round->delete();
        
        return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
            ->with('success', 'Ronda eliminada correctamente.');
    }
    
    /**
     * Cambiar el estado de una ronda (activar/desactivar)
     */
    public function updateStatus(Request $request, string $hackathonId, string $roundId)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        $round = HackathonRound::findOrFail($roundId);
        
        // Verificar que la ronda pertenece al hackathon
        if ($round->hackathon_id != $hackathon->hackathon_id) {
            return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                ->with('error', 'La ronda no pertenece a este hackathon.');
        }
        
        // Verificar permisos
        if (Auth::id() !== $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para cambiar el estado de las rondas de este hackathon.');
        }
        
        $active = $request->input('active') === 'true';
        
        // Si se está activando esta ronda, desactivar las demás
        if ($active && !$round->is_active) {
            DB::transaction(function() use ($hackathon, $round) {
                $hackathon->rounds()->where('round_id', '!=', $round->round_id)->update(['is_active' => false]);
                $round->is_active = true;
                $round->save();
            });
            
            return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                ->with('success', 'Ronda "' . $round->name . '" activada correctamente.');
        } elseif (!$active && $round->is_active) {
            // No permitir desactivar si es la única ronda activa
            $activeRoundsCount = $hackathon->rounds()->where('is_active', true)->count();
            if ($activeRoundsCount <= 1) {
                return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                    ->with('error', 'Debe haber al menos una ronda activa.');
            }
            
            $round->is_active = false;
            $round->save();
            
            return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id)
                ->with('success', 'Ronda "' . $round->name . '" desactivada correctamente.');
        }
        
        return redirect()->route('hackathons.rounds.index', $hackathon->hackathon_id);
    }
}
