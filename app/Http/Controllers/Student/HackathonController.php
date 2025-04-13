<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Hackathon;
use App\Models\HackathonTeam;
use App\Models\HackathonRound;
use App\Models\HackathonDeliverable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HackathonController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Las protecciones de middleware se aplican desde las rutas
    }

    /**
     * Mostrar la lista de hackathones disponibles para el estudiante
     */
    public function index()
    {
        // Obtener hackathones activos
        $activeHackathons = Hackathon::where('status', 'active')
            ->withCount(['teams', 'rounds'])
            ->get();
            
        // Obtener hackathones pasados
        $pastHackathons = Hackathon::where('status', 'finished')
            ->orWhere('end_date', '<', now())
            ->withCount(['teams', 'rounds'])
            ->get();
            
        // Obtener equipos donde participa el estudiante
        $myTeams = HackathonTeam::whereHas('members', function($query) {
            $query->where('hackathon_team_user.user_id', Auth::id());
        })->with(['hackathon', 'members'])->get();
        
        // Marcar los hackathones en los que ya participa el estudiante
        $participatingHackathonIds = $myTeams->pluck('hackathon_id')->toArray();
        
        $activeHackathons->map(function($hackathon) use ($participatingHackathonIds, $myTeams) {
            $hackathon->is_participating = in_array($hackathon->hackathon_id, $participatingHackathonIds);
            if ($hackathon->is_participating) {
                $hackathon->team = $myTeams->where('hackathon_id', $hackathon->hackathon_id)->first();
            }
            return $hackathon;
        });
        
        return view('students.hackathons.index', compact('activeHackathons', 'pastHackathons', 'myTeams'));
    }
    
    /**
     * Mostrar un hackathon específico
     */
    public function show($id)
    {
        $hackathon = Hackathon::with(['rounds', 'judges'])->findOrFail($id);
        
        // Verificar si el estudiante ya está participando
        $team = HackathonTeam::whereHas('members', function($query) {
            $query->where('hackathon_team_user.user_id', Auth::id());
        })->where('hackathon_id', $id)->first();
        
        // Obtener la ronda actual usando getCurrentRound
        $currentRound = $hackathon->getCurrentRound();

        // Si no hay ronda activa, obtener la primera ronda
        if (!$currentRound && $hackathon->rounds->isNotEmpty()) {
            $currentRound = $hackathon->rounds->first();
        }
        
        return view('students.hackathons.show', compact('hackathon', 'team', 'currentRound'));
    }
    
    /**
     * Mostrar el formulario para unirse o crear un equipo
     */
    public function joinForm($id)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar si el hackathon está activo
        if ($hackathon->status !== 'active') {
            return redirect()->route('student.hackathons.details', ['id' => $id])
                ->with('error', 'Este hackathon no está activo para inscripciones.');
        }
        
        // Verificar si el estudiante ya está participando
        $existingTeam = HackathonTeam::whereHas('members', function($query) {
            $query->where('hackathon_team_user.user_id', Auth::id());
        })->where('hackathon_id', $id)->exists();
        
        if ($existingTeam) {
            return redirect()->route('student.hackathons.details', ['id' => $id])
                ->with('error', 'Ya estás participando en este hackathon.');
        }
        
        // Obtener equipos disponibles para unirse - Corregido para usar id en lugar de team_id
        $availableTeams = HackathonTeam::where('hackathon_id', $id)
            ->whereRaw('(SELECT COUNT(*) FROM hackathon_team_user WHERE team_id = hackathon_teams.id) < ?', [$hackathon->team_size])
            ->with(['members'])
            ->get();
            
        return view('students.hackathons.join', compact('hackathon', 'availableTeams'));
    }
    
    /**
     * Crear un nuevo equipo
     */
    public function createTeam(Request $request, $id)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar si el estudiante ya participa en algún equipo de este hackathon
        $participatingTeam = HackathonTeam::whereHas('members', function($query) {
            $query->where('hackathon_team_user.user_id', Auth::id());
        })->where('hackathon_id', $id)->first();
        
        if (!$participatingTeam) {
            // Verificar si hay espacio para más equipos
            $currentTeamsCount = HackathonTeam::where('hackathon_id', $id)->count();
            
            if ($currentTeamsCount < $hackathon->max_teams) {
                $request->validate([
                    'team_name' => 'required|string|max:100',
                    'team_description' => 'required|string|max:500',
                ]);
                
                // Crear el equipo
                $team = HackathonTeam::create([
                    'hackathon_id' => $id,
                    'name' => $request->team_name,
                    'description' => $request->team_description,
                    'leader_id' => Auth::id(),
                    'created_at' => now(),
                ]);
                
                // Verificar si hay espacio para el usuario como miembro
                $isTeamFull = $team->members()->count() >= $hackathon->team_size;
                
                if (!$isTeamFull) {
                    $team->members()->attach(Auth::id(), ['is_leader' => true]);
                    $team->leader_id = Auth::id();
                    $team->save();
                    
                    return redirect()->route('student.hackathons.team', ['id' => $team->id])
                        ->with('success', 'Equipo creado correctamente. ¡Ya puedes comenzar a trabajar en tu proyecto!');
                } else {
                    return redirect()->route('student.hackathons.details', ['id' => $id])
                        ->with('error', 'No se pudo crear el equipo porque ya se alcanzó el límite de miembros.');
                }
            } else {
                return redirect()->route('student.hackathons.details', ['id' => $id])
                    ->with('error', 'No se pudo crear el equipo. Ya se alcanzó el límite de equipos para este hackathon.');
            }
        }
        
        return redirect()->route('student.hackathons.details', ['id' => $id])
            ->with('error', 'Ya estás participando en este hackathon. No puedes crear otro equipo.');
    }
    
    /**
     * Unirse a un equipo existente
     */
    public function joinTeam(Request $request, $id)
    {
        $hackathon = Hackathon::findOrFail($id);
        
        // Verificar si el estudiante ya participa en algún equipo de este hackathon
        $participatingTeam = HackathonTeam::whereHas('members', function($query) {
            $query->where('hackathon_team_user.user_id', Auth::id());
        })->where('hackathon_id', $id)->first();
        
        if (!$participatingTeam) {
            try {
                $request->validate([
                    'team_id' => 'required|exists:hackathon_teams,id',
                ]);
                
                $team = HackathonTeam::findOrFail($request->team_id);
                
                // Verificar si el equipo pertenece a este hackathon
                if ($team->hackathon_id != $id) {
                    return redirect()->route('student.hackathons.details', ['id' => $id])
                        ->with('error', 'El equipo seleccionado no pertenece a este hackathon.');
                }
                
                // Verificar si hay espacio en el equipo
                $isTeamFull = $team->members()->count() >= $hackathon->team_size;
                
                if (!$isTeamFull) {
                    $team->members()->attach(Auth::id());
                    
                    return redirect()->route('student.hackathons.team', ['id' => $team->id])
                        ->with('success', 'Te has unido al equipo correctamente.');
                } else {
                    return redirect()->route('student.hackathons.details', ['id' => $id])
                        ->with('error', 'No puedes unirte a este equipo porque ya está lleno.');
                }
            } catch (\Exception $e) {
                Log::error('Error al unirse al equipo: ' . $e->getMessage(), [
                    'user_id' => Auth::id(),
                    'hackathon_id' => $id,
                    'team_id' => $request->team_id ?? 'No proporcionado'
                ]);
                
                return redirect()->route('student.hackathons.details', ['id' => $id])
                    ->with('error', 'Ha ocurrido un error al intentar unirte al equipo. Detalles: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('student.hackathons.details', ['id' => $id])
            ->with('error', 'Ya estás participando en este hackathon. No puedes unirte a otro equipo.');
    }
    
    /**
     * Dejar un equipo
     */
    public function leaveTeam($teamId)
    {
        $team = HackathonTeam::with('hackathon')->findOrFail($teamId);
        $hackathonId = $team->hackathon_id;
        
        // Verificar si el usuario pertenece al equipo
        if ($team->members->contains(Auth::id())) {
            // Solo se puede abandonar un equipo si el hackathon está activo
            if ($team->hackathon->status != 'active') {
                return redirect()->route('student.hackathons.team', ['id' => $teamId])
                    ->with('error', 'No puedes abandonar el equipo porque el hackathon no está activo.');
            }
            
            try {
                $team->members()->detach(Auth::id());
                
                // Si era el líder, asignar nuevo líder
                if ($team->leader_id === Auth::id()) {
                    $newLeader = $team->members()->first();
                    if ($newLeader) {
                        $team->leader_id = $newLeader->id;
                        $team->save();
                    }
                }
                
                // Si el equipo quedó vacío, eliminarlo
                if ($team->members()->count() === 0) {
                    $team->delete();
                }
                
                return redirect()->route('student.hackathons.details', ['id' => $hackathonId])
                    ->with('success', 'Has abandonado el equipo correctamente.');
            } catch (\Exception $e) {
                return redirect()->route('student.hackathons.details', ['id' => $team->hackathon_id])
                    ->with('error', 'No se pudo abandonar el equipo. Error: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('student.hackathons.details', ['id' => $team->hackathon_id])
            ->with('error', 'No perteneces a este equipo.');
    }
    
    /**
     * Transferir liderazgo a otro miembro
     */
    public function transferLeadership(Request $request, $id)
    {
        $team = HackathonTeam::findOrFail($id);
        
        // Verificar que el usuario actual es el líder
        if (!$team->isLeader(Auth::id())) {
            return redirect()->route('student.hackathons.team', $id)
                ->with('error', 'Solo el líder del equipo puede transferir el liderazgo.');
        }
        
        // Validar datos del formulario
        $request->validate([
            'new_leader_id' => 'required|exists:users,user_id',
        ]);
        
        // Verificar que el nuevo líder pertenece al equipo
        if (!$team->members->contains('user_id', $request->new_leader_id)) {
            return redirect()->route('student.hackathons.team', $id)
                ->with('error', 'El usuario seleccionado no pertenece a este equipo.');
        }
        
        DB::beginTransaction();
        
        try {
            // Quitar liderazgo al líder actual
            DB::table('hackathon_team_user')
                ->where('team_id', $id)
                ->where('user_id', Auth::id())
                ->update(['is_leader' => false]);
            
            // Asignar liderazgo al nuevo líder
            DB::table('hackathon_team_user')
                ->where('team_id', $id)
                ->where('user_id', $request->new_leader_id)
                ->update(['is_leader' => true]);
            
            DB::commit();
            
            return redirect()->route('student.hackathons.team', $id)
                ->with('success', 'Liderazgo transferido correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al transferir el liderazgo: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar detalles del equipo del estudiante
     */
    public function team($id)
    {
        $team = HackathonTeam::with([
            'members',
            'hackathon',
            'hackathon.rounds',
            'deliverables' => function($query) {
                $query->latest();
            }
        ])->findOrFail($id);
        
        // Verificar que el estudiante pertenece al equipo
        if (!$team->members->contains('user_id', Auth::id()) && Auth::user()->role !== 'admin') {
            return redirect()->route('student.hackathons.index')
                ->with('error', 'No tienes permiso para acceder a este equipo.');
        }
        
        // Verificar si el estudiante es líder
        $isLeader = $team->isLeader(Auth::id());
        
        // Obtener la ronda actual usando getCurrentRound
        $currentRound = $team->hackathon->getCurrentRound();
        
        // Si no hay ronda activa, obtener la primera ronda
        if (!$currentRound && $team->hackathon->rounds->isNotEmpty()) {
            $currentRound = $team->hackathon->rounds->first();
        }
        
        // Obtener entregables de la ronda actual
        $currentDeliverables = collect();
        if ($currentRound) {
            $currentDeliverables = $team->deliverables()
                ->where('round_id', $currentRound->round_id)
                ->latest()
                ->get();
        }
        
        return view('students.hackathons.team', compact('team', 'isLeader', 'currentRound', 'currentDeliverables'));
    }
    
    /**
     * Subir un entregable
     */
    public function submitDeliverable(Request $request, $teamId)
    {
        $team = HackathonTeam::findOrFail($teamId);
        
        // Verificar que el estudiante pertenece al equipo
        if (!$team->members->contains('user_id', Auth::id())) {
            return redirect()->route('student.hackathons.index')
                ->with('error', 'No tienes permiso para subir entregables a este equipo.');
        }
        
        // Validar los datos del formulario
        $request->validate([
            'round_id' => 'required|exists:hackathon_rounds,round_id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|max:10240', // Max 10MB
            'repository_url' => 'nullable|url',
        ]);
        
        // Verificar que la ronda pertenece al hackathon
        $round = HackathonRound::where('round_id', $request->round_id)
            ->where('hackathon_id', $team->hackathon_id)
            ->first();
            
        if (!$round) {
            return redirect()->back()
                ->with('error', 'La ronda seleccionada no pertenece a este hackathon.');
        }
        
        // Verificar que se pueden subir entregables a esta ronda
        $debugInfo = $round->debugCanSubmitDeliverables(Auth::id());
        
        if (!$debugInfo['can_submit']) {
            return redirect()->back()
                ->with('error', 'No se pueden subir entregables a esta ronda: ' . $debugInfo['reason']);
        }
        
        // Crear un nuevo entregable
        $deliverable = new HackathonDeliverable();
        $deliverable->team_id = $teamId;
        $deliverable->round_id = $request->round_id;
        $deliverable->user_id = Auth::id(); // Agregar el ID del usuario que sube el entregable
        $deliverable->title = $request->title;
        $deliverable->description = $request->description;
        // No usar repository_url que no existe en la tabla
        // En su lugar, podemos incluirlo en la descripción si es necesario
        if ($request->repository_url) {
            $deliverable->description .= "\n\nRepositorio: " . $request->repository_url;
        }
        
        // Procesar y guardar el archivo si se ha subido
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('hackathon-deliverables/' . $teamId, $fileName, 'public');
            
            $deliverable->file_path = $filePath;
            $deliverable->file_name = $file->getClientOriginalName();
            $deliverable->file_type = $file->getClientMimeType();
            $deliverable->file_size = $file->getSize();
        }
        
        $deliverable->save();
        
        return redirect()->route('student.hackathons.team', $teamId)
            ->with('success', 'Entregable subido correctamente.');
    }
    
    /**
     * Invitar a un miembro al equipo
     */
    public function inviteTeamMember(Request $request, $teamId)
    {
        $team = HackathonTeam::findOrFail($teamId);
        
        // Verificar que el estudiante pertenece al equipo
        if (!$team->members->contains('user_id', Auth::id())) {
            return redirect()->route('student.hackathons.index')
                ->with('error', 'No tienes permiso para invitar miembros a este equipo.');
        }
        
        // Validar los datos del formulario
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        
        // Buscar el usuario por email
        $invitedUser = User::where('email', $request->email)->first();
        
        // Verificar que el usuario no esté ya en el equipo
        if ($team->members->contains('user_id', $invitedUser->user_id)) {
            return redirect()->back()
                ->with('error', 'Este usuario ya es miembro del equipo.');
        }
        
        // Verificar que el usuario no esté ya en otro equipo de este hackathon
        $existingTeam = HackathonTeam::whereHas('members', function($query) use ($invitedUser) {
            $query->where('hackathon_team_user.user_id', $invitedUser->user_id);
        })->where('hackathon_id', $team->hackathon_id)->exists();
        
        if ($existingTeam) {
            return redirect()->back()
                ->with('error', 'Este usuario ya pertenece a otro equipo en este hackathon.');
        }
        
        // Verificar que el equipo no esté lleno
        if ($team->members->count() >= $team->hackathon->team_size) {
            return redirect()->back()
                ->with('error', 'El equipo ya está completo.');
        }
        
        // TODO: Implementar lógica de invitación con correo electrónico
        // Por ahora, añadimos directamente al usuario al equipo
        $team->members()->attach($invitedUser->user_id, ['is_leader' => false]);
        
        return redirect()->route('student.hackathons.team', $teamId)
            ->with('success', 'Usuario añadido al equipo correctamente.');
    }
    
    /**
     * Actualizar información del equipo
     */
    public function updateTeam(Request $request, $teamId)
    {
        $team = HackathonTeam::findOrFail($teamId);
        
        // Verificar que el estudiante es líder del equipo
        if (!$team->isLeader(Auth::id())) {
            return redirect()->route('student.hackathons.team', $teamId)
                ->with('error', 'Solo el líder del equipo puede editar la información.');
        }
        
        // Validar los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        // Actualizar los datos del equipo
        $team->name = $request->name;
        $team->description = $request->description;
        $team->save();
        
        return redirect()->route('student.hackathons.team', $teamId)
            ->with('success', 'Información del equipo actualizada correctamente.');
    }
    
    /**
     * Eliminar a un miembro del equipo
     */
    public function removeMember($teamId, $memberId)
    {
        $team = HackathonTeam::findOrFail($teamId);
        
        // Verificar que el estudiante es líder del equipo
        if (!$team->isLeader(Auth::id())) {
            return redirect()->route('student.hackathons.team', $teamId)
                ->with('error', 'Solo el líder del equipo puede eliminar miembros.');
        }
        
        // Verificar que el miembro a eliminar no es el líder
        if ($team->isLeader($memberId)) {
            return redirect()->route('student.hackathons.team', $teamId)
                ->with('error', 'No puedes eliminar al líder del equipo.');
        }
        
        // Eliminar al miembro del equipo
        $team->members()->detach($memberId);
        
        return redirect()->route('student.hackathons.team', $teamId)
            ->with('success', 'Miembro eliminado del equipo correctamente.');
    }

    /**
     * Generar y descargar certificado de participación o ganador
     */
    public function downloadCertificate(Request $request, $teamId)
    {
        $team = HackathonTeam::with(['hackathon', 'members'])->findOrFail($teamId);
        
        // Verificar que el estudiante pertenece al equipo
        if (!$team->members->contains('user_id', Auth::id())) {
            return redirect()->route('student.hackathons.index')
                ->with('error', 'No tienes permiso para descargar este certificado.');
        }
        
        // Verificar que el hackathon ha finalizado
        if ($team->hackathon->status !== 'finished') {
            return redirect()->route('student.hackathons.team', $teamId)
                ->with('error', 'Solo puedes descargar certificados de hackathones finalizados.');
        }
        
        // Validar tipo de certificado
        $type = $request->input('type', 'participation');
        
        // Si es certificado de ganador, verificar que el equipo es ganador
        if ($type === 'winner' && !$team->is_winner) {
            return redirect()->route('student.hackathons.team', $teamId)
                ->with('error', 'Este equipo no ha sido declarado ganador del hackathon.');
        }
        
        // Obtener datos del usuario
        $user = Auth::user();
        
        // Preparar datos para el certificado
        $certificateData = [
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'team_name' => $team->name,
            'hackathon_name' => $team->hackathon->title,
            'hackathon_date' => \Carbon\Carbon::parse($team->hackathon->end_date)->format('d/m/Y'),
            'type' => $type,
            'position' => $team->position ?? ($team->is_winner ? '1° Lugar' : 'Participante'),
        ];
        
        // En una implementación real, aquí generaríamos un PDF con la biblioteca TCPDF, FPDF o similar
        // Por ahora, simularemos la descarga mostrando un mensaje
        
        // Redirigir de vuelta con un mensaje de éxito (simulando la descarga)
        return redirect()->route('student.hackathons.team', $teamId)
            ->with('success', 'El certificado ha sido generado y descargado.');
    }

    /**
     * Mostrar formulario de edición del equipo
     */
    public function edit($id)
    {
        $team = HackathonTeam::findOrFail($id);
        
        // Verificar que el estudiante pertenece al equipo y es líder
        if (!$team->members->contains('user_id', Auth::id()) || !$team->isLeader(Auth::id())) {
            return redirect()->route('student.hackathons.index')
                ->with('error', 'No tienes permiso para editar este equipo.');
        }
        
        return view('students.hackathons.team-edit', compact('team'));
    }
    
    /**
     * Método para manejar la redirección al repositorio del equipo
     */
    public function repository($id)
    {
        $team = HackathonTeam::findOrFail($id);
        
        // Verificar que el estudiante pertenece al equipo
        if (!$team->members->contains('user_id', Auth::id())) {
            return redirect()->route('student.hackathons.index')
                ->with('error', 'No tienes permiso para acceder al repositorio de este equipo.');
        }
        
        if (!$team->repository_url) {
            return redirect()->route('student.hackathons.team', $team->team_id)
                ->with('error', 'Este equipo no tiene un repositorio configurado.');
        }
        
        return redirect($team->repository_url);
    }
} 