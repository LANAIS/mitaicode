<?php

namespace App\Http\Controllers;

use App\Models\Hackathon;
use App\Models\HackathonDeliverable;
use App\Models\HackathonRound;
use App\Models\HackathonTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HackathonDeliverableController extends Controller
{
    /**
     * Display the team deliverables page.
     *
     * @param  int  $teamId
     * @return \Illuminate\View\View
     */
    public function index($teamId)
    {
        // Verificar si el usuario tiene acceso al equipo
        $team = \App\Models\HackathonTeam::with(['hackathon', 'members', 'leader'])
            ->findOrFail($teamId);
        
        // Comprobar si el usuario es miembro del equipo
        $isMember = $team->members->contains(Auth::id());
        if (!$isMember && Auth::user()->role !== 'admin' && Auth::user()->role !== 'teacher') {
            return redirect()->route('student.hackathons.index')
                ->with('error', 'No tienes permiso para acceder a los entregables de este equipo.');
        }
        
        // Obtener la ronda actual del hackathon usando current_round (que es el ID)
        $currentRound = null;
        if ($team->hackathon->current_round) {
            $currentRound = HackathonRound::where('hackathon_id', $team->hackathon_id)
                ->where('round_id', $team->hackathon->current_round)
                ->first();
        }
        
        // Obtener entregables de la ronda actual
        $deliverables = HackathonDeliverable::where('team_id', $teamId);
        
        if ($currentRound) {
            $deliverables = $deliverables->where('round_id', $currentRound->round_id);
        }
        
        $deliverables = $deliverables->with('user')
            ->latest()
            ->get();
        
        // Obtener entregables de rondas anteriores
        $pastDeliverables = HackathonDeliverable::where('team_id', $teamId);
        
        if ($currentRound) {
            $pastDeliverables = $pastDeliverables->where('round_id', '!=', $currentRound->round_id);
        }
        
        $pastDeliverables = $pastDeliverables->with(['user', 'round'])
            ->latest()
            ->get();
        
        // Añadir información adicional para la vista
        $hackathon = $team->hackathon;
        $rounds = $hackathon->rounds;
        
        return view('students.team-deliverables', compact(
            'team', 
            'currentRound', 
            'deliverables',
            'pastDeliverables',
            'hackathon',
            'rounds'
        ));
    }
    
    /**
     * Upload a new deliverable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upload(Request $request, $teamId)
    {
        $team = HackathonTeam::findOrFail($teamId);
        
        // Verificar que el usuario pertenece al equipo
        if (!$team->members->contains(Auth::id())) {
            return redirect()->route('student.hackathons.team.deliverables', $teamId)
                ->with('error', 'No tienes permiso para subir entregables a este equipo.');
        }
        
        // Validar la entrada del formulario
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'required|file|max:20480', // 20MB máximo
            'round_id' => 'required|exists:hackathon_rounds,id',
        ]);
        
        // Verificar que la ronda pertenece al hackathon
        $round = HackathonRound::findOrFail($validated['round_id']);
        if ($round->hackathon_id !== $team->hackathon_id) {
            return redirect()->route('student.hackathons.team.deliverables', $teamId)
                ->with('error', 'La ronda especificada no pertenece a este hackathon.');
        }
        
        // Generar un nombre único para el archivo
        $fileName = Auth::id() . '_' . time() . '_' . Str::slug($validated['title']) . '.' . $request->file('file')->getClientOriginalExtension();
        
        // Guardar el archivo en el almacenamiento
        $filePath = $request->file('file')->storeAs(
            'hackathon_deliverables/' . $team->hackathon_id . '/' . $teamId,
            $fileName,
            'public'
        );
        
        // Crear el registro del entregable
        HackathonDeliverable::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $filePath,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_type' => $request->file('file')->getClientMimeType(),
            'file_size' => $request->file('file')->getSize(),
            'team_id' => $teamId,
            'user_id' => Auth::id(),
            'round_id' => $validated['round_id'],
        ]);
        
        return redirect()->route('student.hackathons.team.deliverables', $teamId)
            ->with('success', 'Entregable subido correctamente.');
    }
    
    /**
     * Download a deliverable file.
     *
     * @param  int  $deliverableId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($deliverableId)
    {
        $deliverable = HackathonDeliverable::findOrFail($deliverableId);
        $team = HackathonTeam::findOrFail($deliverable->team_id);
        
        // Verificar que el usuario pertenece al equipo o es el profesor/admin
        $isTeacher = Auth::user()->role === 'teacher' &&
                   $team->hackathon->created_by === Auth::id();
                   
        if (!$team->members->contains(Auth::id()) && 
            !$isTeacher && 
            Auth::user()->role !== 'admin') {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para descargar este archivo.');
        }
        
        // Verificar que el archivo existe
        if (!Storage::disk('public')->exists($deliverable->file_path)) {
            return redirect()->route('student.hackathons.team.deliverables', $deliverable->team_id)
                ->with('error', 'El archivo no se encuentra disponible.');
        }
        
        return response()->download(
            Storage::disk('public')->path($deliverable->file_path),
            $deliverable->file_name
        );
    }
    
    /**
     * View for evaluating deliverables (Teacher/Admin).
     *
     * @param  int  $hackathonId
     * @param  int  $roundId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function evaluateIndex($hackathonId, $roundId = null)
    {
        $hackathon = Hackathon::findOrFail($hackathonId);
        
        // Verificar permisos: administrador, profesor creador o juez del hackathon
        if (Auth::user()->role !== 'admin' && 
            (Auth::user()->role !== 'teacher' || $hackathon->created_by !== Auth::id()) &&
            !$hackathon->isJudge(Auth::id())) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para evaluar entregables.');
        }
        
        // Si no se especifica una ronda, usar la actual
        if (!$roundId) {
            // Obtener la ronda basada en el campo current_round del hackathon
            // que almacena el ID de la ronda actual, no el número
            if ($hackathon->current_round) {
                $round = HackathonRound::where('hackathon_id', $hackathonId)
                    ->where('round_id', $hackathon->current_round)
                    ->first();
                    
                if ($round) {
                    $roundId = $round->round_id;
                }
            }
        }
        
        // Obtener todas las rondas del hackathon
        $rounds = HackathonRound::where('hackathon_id', $hackathonId)
            ->get();
            
        // Si aún no tenemos roundId y hay rondas disponibles, usar la primera
        if (!$roundId && $rounds->count() > 0) {
            $roundId = $rounds->first()->round_id;
        }
        
        // Obtener todos los equipos del hackathon
        $teams = HackathonTeam::with(['members', 'leader'])
            ->where('hackathon_id', $hackathonId)
            ->get();
            
        // Para cada equipo, cargar los entregables de la ronda seleccionada
        foreach ($teams as $team) {
            if ($roundId) {
                $team->deliverables = HackathonDeliverable::with(['user', 'round'])
                    ->where('team_id', $team->team_id)
                    ->where('round_id', $roundId)
                    ->latest()
                    ->get();
            } else {
                $team->deliverables = collect([]);
            }
        }
        
        return view('teachers.evaluate-deliverables', compact(
            'hackathon',
            'rounds',
            'roundId',
            'teams'
        ));
    }
    
    /**
     * Save evaluation for a deliverable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $deliverableId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveEvaluation(Request $request, $deliverableId)
    {
        $deliverable = HackathonDeliverable::with('team.hackathon')->findOrFail($deliverableId);
        $hackathon = $deliverable->team->hackathon;
        
        // Verificar permisos: administrador, profesor creador o juez del hackathon
        if (Auth::user()->role !== 'admin' && 
            (Auth::user()->role !== 'teacher' || $hackathon->created_by !== Auth::id()) &&
            !$hackathon->isJudge(Auth::id())) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para evaluar entregables.');
        }
        
        // Validar la entrada
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:10',
            'feedback' => 'nullable|string|max:1000',
        ]);
        
        // Actualizar el entregable con la evaluación
        $deliverable->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'evaluated_at' => now(),
            'evaluated_by' => Auth::id(),
        ]);
        
        return redirect()->route('hackathons.deliverables.evaluate.round', ['hackathonId' => $hackathon->id, 'roundId' => $deliverable->round_id])
            ->with('success', 'Evaluación guardada correctamente.');
    }
} 