@extends('layouts.app')

@section('title', $team->name . ' - ' . $team->hackathon->title)

@section('header', 'Equipo de Hackathon')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.hackathons.index') }}">Hackathones</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.hackathons.details', ['id' => $team->hackathon->id]) }}">{{ $team->hackathon->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $team->team_name }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('student.hackathons.details', ['id' => $team->hackathon->id]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al hackathon
            </a>
            @if($team->hackathon->status === 'active')
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteModal">
                    <i class="fas fa-user-plus"></i> Invitar
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Información del equipo -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $team->name }}</h5>
                    <span class="badge bg-light text-primary">
                        {{ $team->members->count() }} de {{ $team->hackathon->team_size }} miembros
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Descripción del equipo:</h6>
                        <p>{{ $team->description }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Hackathon: {{ $team->hackathon->title }}</h6>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <i class="fas fa-calendar me-1 text-primary"></i> 
                                <strong>Fechas:</strong> 
                                {{ \Carbon\Carbon::parse($team->hackathon->start_date)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($team->hackathon->end_date)->format('d/m/Y') }}
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-flag me-1 text-primary"></i> 
                                <strong>Estado:</strong> 
                                <span class="badge bg-{{ $team->hackathon->status === 'active' ? 'success' : ($team->hackathon->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ $team->hackathon->status === 'active' ? 'Activo' : ($team->hackathon->status === 'pending' ? 'Próximamente' : 'Finalizado') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($isLeader && $team->hackathon->status === 'active')
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editTeamModal">
                                <i class="fas fa-edit me-1"></i> Editar equipo
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Miembros del equipo -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Miembros del equipo</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Miembro</th>
                                    <th scope="col">Rol</th>
                                    <th scope="col">Se unió</th>
                                    @if($isLeader && $team->hackathon->status === 'active')
                                        <th scope="col">Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($team->members as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 36px; height: 36px; overflow: hidden;">
                                                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed={{ $member->user_id ?? 'user' . $loop->index }}" alt="Avatar" style="width: 100%; height: 100%;">
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $member->first_name }} {{ $member->last_name }}</div>
                                                    <small class="text-muted">{{ $member->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($team->isLeader($member->user_id))
                                                <span class="badge bg-primary">Líder</span>
                                            @else
                                                <span class="badge bg-secondary">Miembro</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $member->created_at->format('d/m/Y') }}</small>
                                        </td>
                                        @if($isLeader && $team->hackathon->status === 'active')
                                            <td>
                                                @if(!$team->isLeader($member->user_id) && $member->user_id !== Auth::id())
                                                    <div class="btn-group btn-group-sm">
                                                        <form action="{{ route('student.hackathons.transfer-leadership', $team->team_id) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="new_leader_id" value="{{ $member->user_id }}">
                                                            <button type="submit" class="btn btn-outline-primary btn-sm" title="Transferir liderazgo"
                                                                    onclick="return confirm('¿Estás seguro de que deseas transferir el liderazgo del equipo a {{ $member->first_name }}? Esta acción no se puede deshacer.')">
                                                                <i class="fas fa-crown"></i>
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-outline-danger btn-sm ms-1" title="Eliminar del equipo" 
                                                                data-bs-toggle="modal" data-bs-target="#removeMemberModal{{ $member->user_id }}">
                                                            <i class="fas fa-user-minus"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Modal para eliminar miembro -->
                                                    <div class="modal fade" id="removeMemberModal{{ $member->user_id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Eliminar miembro del equipo</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>¿Estás seguro de que deseas eliminar a <strong>{{ $member->first_name }} {{ $member->last_name }}</strong> del equipo?</p>
                                                                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <form action="{{ route('student.hackathons.remove-member', [$team->team_id, $member->user_id]) }}" method="POST">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($member->user_id === Auth::id())
                                                    <form action="{{ route('student.hackathons.leave', $team->hackathon_id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Abandonar equipo"
                                                                onclick="return confirm('¿Estás seguro de que deseas abandonar el equipo? Como líder, primero debes transferir el liderazgo a otro miembro.')">
                                                            <i class="fas fa-sign-out-alt"></i> Abandonar
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($team->members->count() < $team->hackathon->team_size && $team->hackathon->status === 'active')
                        <div class="alert alert-info mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-info-circle me-2"></i> 
                                    El equipo puede tener {{ $team->hackathon->team_size - $team->members->count() }} miembros más.
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteModal">
                                    <i class="fas fa-user-plus me-1"></i> Invitar miembros
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Entregables del equipo -->
            <div class="card shadow-sm mb-4" id="deliverables">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Entregables</h5>
                    @if($currentRound && $currentRound->isCurrentlyActive() && $team->hackathon->status === 'active')
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#submitDeliverableModal">
                            <i class="fas fa-upload me-1"></i> Subir entregable
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($currentRound)
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading">Ronda actual: {{ $currentRound->name }}</h5>
                                    <p class="mb-1">{{ $currentRound->description }}</p>
                                    <div class="d-flex mt-2">
                                        <div class="me-3">
                                            <i class="fas fa-calendar-alt me-1"></i> 
                                            <strong>Fechas:</strong> 
                                            {{ \Carbon\Carbon::parse($currentRound->start_date)->format('d/m/Y') }} - 
                                            {{ \Carbon\Carbon::parse($currentRound->end_date)->format('d/m/Y') }}
                                        </div>
                                        <div>
                                            <i class="fas fa-clock me-1"></i> 
                                            <strong>Estado:</strong> 
                                            @if($currentRound->hasNotStarted())
                                                <span class="badge bg-warning text-dark">Próximamente</span>
                                            @elseif($currentRound->hasEnded())
                                                <span class="badge bg-secondary">Finalizada</span>
                                            @else
                                                <span class="badge bg-success">En curso</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($currentRound->isCurrentlyActive())
                                        @php
                                            $daysLeft = \Carbon\Carbon::now()->diffInDays($currentRound->end_date, false);
                                        @endphp
                                        <div class="progress mt-2" style="height: 8px;">
                                            @php
                                                $totalDays = $currentRound->start_date->diffInDays($currentRound->end_date);
                                                $passedDays = $currentRound->start_date->diffInDays(now());
                                                $progress = $totalDays > 0 ? ($passedDays / $totalDays) * 100 : 0;
                                                $progress = min(100, max(0, $progress));
                                            @endphp
                                            <div class="progress-bar progress-bar-striped bg-success" 
                                                 role="progressbar" 
                                                 style="width: {{ $progress }}%" 
                                                 aria-valuenow="{{ $progress }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100"></div>
                                        </div>
                                        <div class="text-end mt-1">
                                            <small class="text-muted">
                                                @if($daysLeft > 0)
                                                    <strong>{{ $daysLeft }}</strong> días restantes
                                                @else
                                                    Finaliza hoy
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Entregables de la ronda actual -->
                        <h6 class="mt-4 mb-3">Entregables de la ronda actual</h6>
                        @if($currentDeliverables->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Título</th>
                                            <th scope="col">Subido</th>
                                            <th scope="col">Estado</th>
                                            <th scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($currentDeliverables as $deliverable)
                                            <tr>
                                                <td>{{ $deliverable->title }}</td>
                                                <td><small class="text-muted">{{ $deliverable->created_at->format('d/m/Y H:i') }}</small></td>
                                                <td>
                                                    @if($deliverable->isEvaluated())
                                                        <span class="badge bg-success">Evaluado ({{ $deliverable->score }})</span>
                                                    @else
                                                        <span class="badge bg-secondary">Pendiente</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#viewDeliverableModal{{ $deliverable->deliverable_id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <!-- Modal para ver entregable -->
                                                    <div class="modal fade" id="viewDeliverableModal{{ $deliverable->deliverable_id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">{{ $deliverable->title }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <h6>Descripción:</h6>
                                                                        <p>{{ $deliverable->description }}</p>
                                                                    </div>
                                                                    
                                                                    @if($deliverable->repository_url)
                                                                        <div class="mb-3">
                                                                            <h6>Repositorio:</h6>
                                                                            <a href="{{ $deliverable->repository_url }}" target="_blank" class="d-block">
                                                                                <i class="fas fa-external-link-alt me-1"></i> {{ $deliverable->repository_url }}
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($deliverable->file_path)
                                                                        <div class="mb-3">
                                                                            <h6>Archivo:</h6>
                                                                            <a href="{{ Storage::url($deliverable->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                                                <i class="fas fa-download me-1"></i> Descargar archivo
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                    
                                                                    @if($deliverable->isEvaluated())
                                                                        <div class="alert alert-success mt-3">
                                                                            <h6 class="alert-heading">Evaluación:</h6>
                                                                            <p class="mb-1">
                                                                                <strong>Puntaje:</strong> {{ $deliverable->score }} / 5
                                                                            </p>
                                                                            @if($deliverable->feedback)
                                                                                <p class="mb-0">
                                                                                    <strong>Feedback:</strong> {{ $deliverable->feedback }}
                                                                                </p>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-secondary">
                                <i class="fas fa-info-circle me-2"></i> 
                                No hay entregables para la ronda actual.
                                @if($currentRound->isCurrentlyActive() && $team->hackathon->status === 'active')
                                    <div class="d-flex justify-content-center mt-2">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitDeliverableModal">
                                            <i class="fas fa-upload me-1"></i> Subir mi primer entregable
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> 
                            No hay rondas configuradas para este hackathon todavía.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Rondas del hackathon -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Progreso del Hackathon</h5>
                </div>
                <div class="card-body">
                    @if($team->hackathon->rounds->count() > 0)
                        <div class="progress mb-3" style="height: 10px;">
                            @php
                                $completedRounds = $team->hackathon->rounds->filter(function($round) {
                                    return $round->hasEnded();
                                })->count();
                                
                                $progress = ($completedRounds / $team->hackathon->rounds->count()) * 100;
                            @endphp
                            <div class="progress-bar bg-success" 
                                 role="progressbar" 
                                 style="width: {{ $progress }}%" 
                                 aria-valuenow="{{ $progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">{{ $completedRounds }} de {{ $team->hackathon->rounds->count() }} rondas completadas</small>
                        
                        <ul class="list-group list-group-flush mt-3">
                            @foreach($team->hackathon->rounds as $index => $round)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-{{ $round->is_active ? 'primary fw-bold' : 'dark' }}">
                                            Ronda {{ $index + 1 }}: {{ $round->name }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @if($round->hasEnded())
                                            <span class="badge bg-secondary me-2">Finalizada</span>
                                        @elseif($round->isCurrentlyActive())
                                            <span class="badge bg-success me-2">En curso</span>
                                        @else
                                            <span class="badge bg-light text-dark me-2">Próxima</span>
                                        @endif
                                        
                                        @if($round->isCurrentlyActive() && $team->hackathon->status === 'active')
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" data-bs-target="#submitDeliverableModal{{ $round->round_id }}">
                                                <i class="fas fa-upload"></i>
                                            </button>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">
                            No hay rondas configuradas para este hackathon todavía.
                        </p>
                    @endif
                </div>
            </div>
            
            <!-- Acciones del equipo -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    @if($team->hackathon->status === 'active')
                        <div class="d-grid gap-2">
                            @if($currentRound && $currentRound->isCurrentlyActive())
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#submitDeliverableModal{{ $currentRound->round_id }}">
                                    <i class="fas fa-upload me-1"></i> Subir entregable
                                </button>
                            @endif
                            
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#inviteModal">
                                <i class="fas fa-user-plus me-1"></i> Invitar miembros
                            </button>
                            
                            <form action="{{ route('student.hackathons.leave', $team->hackathon_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('¿Estás seguro de que deseas abandonar este equipo?')">
                                    <i class="fas fa-sign-out-alt me-1"></i> Abandonar equipo
                                </button>
                            </form>
                        </div>
                    @elseif($team->hackathon->status === 'finished')
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.hackathons.certificate', ['teamId' => $team->team_id, 'type' => 'participation']) }}" class="btn btn-outline-primary">
                                <i class="fas fa-medal me-1"></i> Certificado de Participación
                            </a>
                            
                            @if($team->is_winner)
                            <a href="{{ route('student.hackathons.certificate', ['teamId' => $team->team_id, 'type' => 'winner']) }}" class="btn btn-outline-success">
                                <i class="fas fa-trophy me-1"></i> Certificado de Ganador
                            </a>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-info-circle me-2"></i> 
                            El hackathon está {{ $team->hackathon->status === 'pending' ? 'por comenzar' : 'finalizado' }}, las acciones del equipo están limitadas.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Miembros del equipo (vista compacta) -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Equipo</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap">
                        @foreach($team->members as $member)
                            <div class="text-center me-3 mb-3" style="width: 70px;">
                                <div class="rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; overflow: hidden;">
                                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed={{ $member->user_id ?? 'user' . $loop->index }}" 
                                         alt="Avatar de {{ $member->first_name }}" 
                                         style="width: 100%; height: 100%;">
                                </div>
                                <small class="d-block text-truncate" title="{{ $member->first_name }} {{ $member->last_name }}">
                                    {{ $member->first_name }}
                                </small>
                                @if($team->isLeader($member->user_id))
                                    <span class="badge bg-primary" style="font-size: 0.6rem;">Líder</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Certificados y logros -->
            @if($team->hackathon->status === 'finished')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Certificados y Logros</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="badge-container">
                            <img src="{{ asset('img/badges/hackathon-participant.png') }}" alt="Participante" class="img-fluid" style="max-width: 100px;">
                            <p class="text-center mt-2">Participante</p>
                        </div>
                        
                        @if($team->is_winner)
                        <div class="badge-container ms-4">
                            <img src="{{ asset('img/badges/hackathon-winner.png') }}" alt="Ganador" class="img-fluid" style="max-width: 100px;">
                            <p class="text-center mt-2">Ganador</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Descarga tus certificados desde la sección de Acciones.
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Modales para subir entregable para cada ronda -->
    @if($team->hackathon->status === 'active')
        @foreach($team->hackathon->rounds as $round)
            @if($round->isCurrentlyActive())
                <div class="modal fade" id="submitDeliverableModal{{ $round->round_id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Subir entregable para {{ $round->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('student.hackathons.submit-deliverable', ['id' => $team->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <input type="hidden" name="round_id" value="{{ $round->round_id }}">
                                    
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Título del entregable <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="repository_url" class="form-label">URL del repositorio (opcional)</label>
                                        <input type="url" class="form-control" id="repository_url" name="repository_url" placeholder="https://github.com/usuario/proyecto">
                                        <div class="form-text">Comparte el enlace al repositorio de tu proyecto si está disponible en GitHub, GitLab, etc.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Archivo (opcional, máximo 10MB)</label>
                                        <input type="file" class="form-control" id="file" name="file">
                                        <div class="form-text">Puedes subir un archivo (documento, presentación, imagen, etc.) relacionado con tu entregable.</div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Consejos para tu entrega:</strong>
                                        <ul class="mb-0 mt-1">
                                            <li>Asegúrate de que tu entrega cumpla con los requisitos de la ronda actual.</li>
                                            <li>Incluye enlaces a demostraciones o videos si es posible.</li>
                                            <li>Sé claro y conciso en tu descripción.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-upload me-1"></i> Subir entregable
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
    
    <!-- Modal para invitar miembros -->
    <div class="modal fade" id="inviteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Invitar miembros al equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('student.hackathons.invite', ['id' => $team->id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="form-text">Ingresa el correo electrónico del estudiante que deseas invitar.</div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Se enviará una invitación al estudiante. Deberá aceptarla para unirse al equipo.
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Enviar invitación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para editar equipo -->
    @if($isLeader)
        <div class="modal fade" id="editTeamModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar equipo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('student.hackathons.update-team', ['id' => $team->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="team_name" class="form-label">Nombre del equipo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="team_name" name="name" value="{{ $team->name }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="team_description" class="form-label">Descripción <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="team_description" name="description" rows="3" required>{{ $team->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 