<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamMessage;
use App\Models\TeamDeliverable;
use App\Models\HackathonRound;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Mostrar el chat del equipo
     */
    public function showChat($teamId)
    {
        $team = Team::with(['hackathon', 'members', 'messages.user'])->findOrFail($teamId);
        
        // Verificar si el usuario es miembro del equipo
        if (!$team->members->contains('id', Auth::id())) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para acceder a este chat de equipo.');
        }
        
        return view('students.team-chat', [
            'team' => $team,
            'messages' => $team->messages->sortBy('created_at')
        ]);
    }
    
    /**
     * Enviar un mensaje en el chat
     */
    public function sendMessage(Request $request, $teamId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        
        $team = Team::findOrFail($teamId);
        
        // Verificar si el usuario es miembro del equipo
        if (!$team->members->contains('id', Auth::id())) {
            return response()->json(['error' => 'No tienes permiso para enviar mensajes en este chat.'], 403);
        }
        
        $message = new TeamMessage([
            'team_id' => $teamId,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);
        
        $message->save();
        
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'text' => $message->message,
                'user' => [
                    'name' => Auth::user()->name,
                    'avatar' => Auth::user()->profile_image ?? asset('img/avatars/default.png'),
                ],
                'time' => $message->created_at->format('H:i'),
                'date' => $message->created_at->format('d/m/Y'),
            ]
        ]);
    }
    
    /**
     * Mostrar página de entregables
     */
    public function showDeliverables($teamId)
    {
        $team = Team::with(['hackathon', 'members', 'deliverables'])->findOrFail($teamId);
        
        // Verificar si el usuario es miembro del equipo
        if (!$team->members->contains('id', Auth::id())) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para acceder a los entregables de este equipo.');
        }
        
        // Obtener la ronda actual
        $currentRound = $team->hackathon->currentRound();
        
        return view('students.team-deliverables', [
            'team' => $team,
            'currentRound' => $currentRound,
            'deliverables' => $team->deliverables->where('round_id', $currentRound->id ?? 0),
            'pastDeliverables' => $team->deliverables->where('round_id', '!=', $currentRound->id ?? 0),
        ]);
    }
    
    /**
     * Subir un entregable
     */
    public function uploadDeliverable(Request $request, $teamId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'file' => 'required|file|max:20480', // 20MB max
            'round_id' => 'required|integer|exists:hackathon_rounds,id',
        ]);
        
        $team = Team::findOrFail($teamId);
        
        // Verificar si el usuario es miembro del equipo
        if (!$team->members->contains('id', Auth::id())) {
            return redirect()->back()->with('error', 'No tienes permiso para subir entregables para este equipo.');
        }
        
        // Verificar si la ronda pertenece al hackathon del equipo
        $round = HackathonRound::where('id', $request->round_id)
            ->where('hackathon_id', $team->hackathon_id)
            ->first();
            
        if (!$round) {
            return redirect()->back()->with('error', 'La ronda seleccionada no pertenece a este hackathon.');
        }
        
        // Procesar y guardar el archivo
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
        
        $filePath = $file->storeAs('team-deliverables/' . $teamId, $fileName, 'public');
        
        // Crear el entregable
        $deliverable = new TeamDeliverable([
            'team_id' => $teamId,
            'user_id' => Auth::id(),
            'round_id' => $request->round_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $originalName,
            'file_type' => $file->getMimeType(),
        ]);
        
        $deliverable->save();
        
        return redirect()->back()->with('success', 'Entregable subido correctamente.');
    }
    
    /**
     * Descargar un entregable
     */
    public function downloadDeliverable($deliverableId)
    {
        $deliverable = TeamDeliverable::findOrFail($deliverableId);
        $team = Team::findOrFail($deliverable->team_id);
        
        // Verificar si el usuario es miembro del equipo o profesor/administrador
        if (!$team->members->contains('id', Auth::id()) && 
            !Auth::user()->hasRole(['teacher', 'admin'])) {
            return redirect()->back()->with('error', 'No tienes permiso para descargar este archivo.');
        }
        
        if (!Storage::disk('public')->exists($deliverable->file_path)) {
            return redirect()->back()->with('error', 'El archivo no se encuentra disponible.');
        }
        
        return Storage::disk('public')->download($deliverable->file_path, $deliverable->file_name);
    }
    
    /**
     * Configurar repositorio del equipo
     */
    public function updateRepository(Request $request, $teamId)
    {
        $request->validate([
            'repository_url' => 'required|url|max:255',
        ]);
        
        $team = Team::findOrFail($teamId);
        
        // Verificar si el usuario es miembro del equipo
        if (!$team->members->contains('id', Auth::id())) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar este equipo.');
        }
        
        $team->repository_url = $request->repository_url;
        $team->save();
        
        return redirect()->back()->with('success', 'URL del repositorio actualizada correctamente.');
    }
    
    /**
     * Ver repositorio del equipo
     */
    public function viewRepository($teamId)
    {
        $team = Team::with(['hackathon', 'members'])->findOrFail($teamId);
        
        // Verificar si el usuario es miembro del equipo
        if (!$team->members->contains('id', Auth::id())) {
            return redirect()->route('hackathons.index')
                ->with('error', 'No tienes permiso para acceder al repositorio de este equipo.');
        }
        
        if (!$team->repository_url) {
            return redirect()->route('hackathons.team.edit', $teamId)
                ->with('info', 'Este equipo aún no tiene un repositorio configurado. Puedes añadir uno aquí.');
        }
        
        // Redirigir al repositorio externo
        return redirect($team->repository_url);
    }
} 