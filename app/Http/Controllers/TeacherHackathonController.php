<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hackathon;
use App\Models\HackathonRound;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TeacherHackathonController extends Controller
{
    /**
     * Constructor para aplicar protección básica
     */
    public function __construct()
    {
        // Los middlewares se aplican desde las rutas (auth y role:teacher,admin)
    }

    /**
     * Mostrar el formulario para crear un hackathon (específico para profesores)
     */
    public function create()
    {
        return view('hackathons.create');
    }

    /**
     * Almacenar un nuevo hackathon (específico para profesores)
     */
    public function store(Request $request)
    {
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
        
        // Iniciar transacción
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
     * Mostrar el formulario para editar un hackathon existente
     */
    public function edit($hackathon_id)
    {
        $hackathon = Hackathon::with(['rounds' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($hackathon_id);
        
        // Verificar permisos
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para editar este hackathon.');
        }
        
        return view('hackathons.edit', compact('hackathon'));
    }

    /**
     * Actualizar un hackathon en la base de datos
     */
    public function update(Request $request, $hackathon_id)
    {
        $hackathon = Hackathon::findOrFail($hackathon_id);
        
        // Verificar permisos
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin') {
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
        
        return redirect()->route('hackathons.show', $hackathon->id)
            ->with('success', 'Hackathon actualizado correctamente.');
    }

    /**
     * Redirige a la gestión de rondas del hackathon
     */
    public function redirectToRounds($hackathon_id)
    {
        $hackathon = Hackathon::findOrFail($hackathon_id);
        
        // Verificar permisos
        if (Auth::id() != $hackathon->created_by && Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para gestionar las rondas de este hackathon.');
        }
        
        // Asegurarnos de usar el campo id correcto para la redirección
        return redirect()->route('hackathons.rounds.index', ['hackathonId' => $hackathon->id]);
    }
} 