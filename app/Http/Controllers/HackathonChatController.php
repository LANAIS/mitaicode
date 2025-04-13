<?php

namespace App\Http\Controllers;

use App\Models\HackathonTeam;
use App\Models\HackathonMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HackathonChatController extends Controller
{
    /**
     * Display the team chat view.
     *
     * @param  int  $teamId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index($teamId)
    {
        $team = HackathonTeam::with(['hackathon', 'members', 'leader'])
            ->findOrFail($teamId);
            
        // Verificar que el usuario pertenece al equipo
        if (!$team->members->contains(Auth::id()) && Auth::user()->role !== 'admin') {
            session()->flash('error', 'No tienes permiso para acceder a este chat.');
            return redirect()->route('student.hackathons.index')
                ->with('error', 'No tienes permiso para acceder a este chat.');
        }
        
        // Obtener mensajes del chat
        $messages = HackathonMessage::where('team_id', $teamId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->take(100)
            ->get();
            
        // Obtener la ronda actual del hackathon
        $currentRound = $team->hackathon->currentRound();
        
        return view('students.team-chat', compact('team', 'messages', 'currentRound'));
    }
    
    /**
     * Send a message to the team chat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function sendMessage(Request $request, $teamId)
    {
        $team = HackathonTeam::findOrFail($teamId);
        
        // Verificar que el usuario pertenece al equipo
        if (!$team->members->contains(Auth::id())) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No tienes permiso para enviar mensajes a este equipo.'], 403);
            }
            
            session()->flash('error', 'No tienes permiso para enviar mensajes a este equipo.');
            return redirect()->route('student.hackathons.team', $teamId)
                ->with('error', 'No tienes permiso para enviar mensajes a este equipo.');
        }
        
        // Validar el mensaje
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);
        
        // Crear el mensaje
        $message = HackathonMessage::create([
            'team_id' => $teamId,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
        ]);
        
        // Cargar la relación de usuario
        $message->load('user');
        
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
                'success' => true,
            ]);
        }
        
        session()->flash('success', 'Mensaje enviado correctamente.');
        return redirect()->route('student.hackathons.team.chat', $teamId)
            ->with('success', 'Mensaje enviado correctamente.');
    }
    
    /**
     * Get messages for a team chat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getMessages(Request $request, $teamId)
    {
        $team = HackathonTeam::findOrFail($teamId);
        
        // Verificar que el usuario pertenece al equipo
        if (!$team->members->contains(Auth::id()) && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'No tienes permiso para ver mensajes de este equipo.'], 403);
        }
        
        // Validar los parámetros de la solicitud
        $validated = $request->validate([
            'last_id' => 'nullable|integer',
            'limit' => 'nullable|integer|max:100',
        ]);
        
        // Construir la consulta
        $query = HackathonMessage::where('team_id', $teamId)
            ->with('user')
            ->orderBy('created_at', 'asc');
            
        // Si se proporciona el ID del último mensaje, obtener solo los más recientes
        if (isset($validated['last_id'])) {
            $query->where('id', '>', $validated['last_id']);
        }
        
        // Limitar la cantidad de mensajes
        $limit = $validated['limit'] ?? 50;
        $messages = $query->take($limit)->get();
        
        return response()->json([
            'messages' => $messages,
            'success' => true,
        ]);
    }
} 