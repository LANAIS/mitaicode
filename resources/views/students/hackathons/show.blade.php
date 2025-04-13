@extends('layouts.app')

@section('title', $hackathon->title)

@section('header', 'Detalles del Hackathon')

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
                    <li class="breadcrumb-item active" aria-current="page">{{ $hackathon->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('student.hackathons.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver a hackathones
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="h3 mb-0">{{ $hackathon->title }}</h1>
                        <span class="badge bg-{{ $hackathon->status === 'active' ? 'success' : ($hackathon->status === 'pending' ? 'warning' : 'secondary') }}">
                            {{ $hackathon->status === 'active' ? 'Activo' : ($hackathon->status === 'pending' ? 'Próximamente' : 'Finalizado') }}
                        </span>
                    </div>
                    
                    @if($hackathon->image)
                        <img src="{{ Storage::url($hackathon->image) }}" class="img-fluid rounded mb-4 hackathon-header-image" alt="{{ $hackathon->title }}">
                    @endif
                    
                    <div class="mb-4">
                        <h5>Descripción</h5>
                        <p>{{ $hackathon->description }}</p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-calendar me-2 text-primary"></i> Fechas</h6>
                                    <p class="card-text">
                                        <strong>Inicio:</strong> {{ \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') }}<br>
                                        <strong>Fin:</strong> {{ \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') }}<br>
                                        <strong>Duración:</strong> {{ \Carbon\Carbon::parse($hackathon->start_date)->diffInDays($hackathon->end_date) }} días
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-users me-2 text-success"></i> Participantes</h6>
                                    <p class="card-text">
                                        <strong>Equipos:</strong> {{ $hackathon->teams()->count() }} de {{ $hackathon->max_teams }}<br>
                                        <strong>Tamaño del equipo:</strong> {{ $hackathon->team_size }} participantes máx.<br>
                                        <strong>Participantes totales:</strong> {{ $hackathon->teams()->count() * $hackathon->team_size }} máx.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($team)
                        <div class="alert alert-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-check-circle me-2"></i> 
                                    Ya estás participando en este hackathon con el equipo <strong>{{ $team->name }}</strong>
                                </div>
                                <a href="{{ route('student.hackathons.team', ['id' => $team->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-users me-1"></i> Ver mi equipo
                                </a>
                            </div>
                        </div>
                    @else
                        @if($hackathon->status === 'active')
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-info-circle me-2"></i> 
                                        No estás participando en este hackathon. ¿Te gustaría unirte?
                                    </div>
                                    @if(isset($hackathon->hackathon_id))
                                    <a href="{{ route('student.hackathons.join', ['id' => $hackathon->id]) }}" class="btn btn-success">
                                        <i class="fas fa-sign-in-alt me-1"></i> Participar
                                    </a>
                                    @else
                                    <button class="btn btn-warning" disabled>
                                        <i class="fas fa-exclamation-triangle me-1"></i> ID no disponible
                                    </button>
                                    @endif
                                </div>
                            </div>
                        @elseif($hackathon->status === 'pending')
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i> 
                                Este hackathon aún no está abierto para inscripciones. Podrás participar una vez que comience.
                            </div>
                        @else
                            <div class="alert alert-secondary">
                                <i class="fas fa-history me-2"></i> 
                                Este hackathon ya ha finalizado y no acepta nuevos participantes.
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Sección de Rondas -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rondas del Hackathon</h5>
                    <span class="badge bg-info">{{ $hackathon->rounds->count() }}</span>
                </div>
                <div class="card-body">
                    @if($hackathon->rounds->count() > 0)
                        <div class="accordion" id="roundsAccordion">
                            @foreach($hackathon->rounds as $index => $round)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $round->round_id }}">
                                        <button class="accordion-button {{ $round->is_active ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $round->round_id }}" aria-expanded="{{ $round->is_active ? 'true' : 'false' }}" aria-controls="collapse{{ $round->round_id }}">
                                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                <span>Ronda {{ $index + 1 }}: {{ $round->name }}</span>
                                                @if($round->is_active)
                                                    <span class="badge bg-success ms-2">Activa</span>
                                                @elseif($round->hasEnded())
                                                    <span class="badge bg-secondary ms-2">Finalizada</span>
                                                @elseif($round->hasNotStarted())
                                                    <span class="badge bg-warning text-dark ms-2">Próxima</span>
                                                @else
                                                    <span class="badge bg-info ms-2">En curso</span>
                                                @endif
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $round->round_id }}" class="accordion-collapse collapse {{ $round->is_active ? 'show' : '' }}" aria-labelledby="heading{{ $round->round_id }}" data-bs-parent="#roundsAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-3">
                                                <p>{{ $round->description }}</p>
                                                <div class="d-flex justify-content-between text-muted small">
                                                    <div>
                                                        <i class="fas fa-calendar-alt me-1"></i> 
                                                        {{ \Carbon\Carbon::parse($round->start_date)->format('d/m/Y') }} - 
                                                        {{ \Carbon\Carbon::parse($round->end_date)->format('d/m/Y') }}
                                                    </div>
                                                    <div>
                                                        <i class="fas fa-clock me-1"></i> 
                                                        {{ \Carbon\Carbon::parse($round->start_date)->diffInDays($round->end_date) }} días
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($round->objectives)
                                                <div class="mb-3">
                                                    <h6>Objetivos:</h6>
                                                    <p>{{ $round->objectives }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($round->deliverables)
                                                <div class="mb-3">
                                                    <h6>Entregables:</h6>
                                                    <p>{{ $round->deliverables }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($round->is_active && $team)
                                                <div class="mt-3">
                                                    <a href="{{ route('student.hackathons.team', ['id' => $team->id]) }}#deliverables" class="btn btn-primary">
                                                        <i class="fas fa-file-upload me-1"></i> Subir entregable
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($currentRound)
                            <div class="mt-4">
                                <h6>Progreso actual:</h6>
                                <div class="progress" style="height: 10px;">
                                    @php
                                        $completedRounds = $hackathon->rounds->filter(function($round) {
                                            return $round->hasEnded();
                                        })->count();
                                        
                                        $progress = ($completedRounds / $hackathon->rounds->count()) * 100;
                                    @endphp
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar" 
                                         style="width: {{ $progress }}%" 
                                         aria-valuenow="{{ $progress }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small>Inicio ({{ \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') }})</small>
                                    <small>Fin ({{ \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') }})</small>
                                </div>
                                
                                @if($currentRound->isCurrentlyActive())
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-hourglass-half me-2"></i> 
                                        Ronda actual: <strong>{{ $currentRound->name }}</strong> 
                                        @php
                                            $daysLeft = \Carbon\Carbon::now()->diffInDays($currentRound->end_date, false);
                                        @endphp
                                        @if($daysLeft > 0)
                                            - Tiempo restante: <strong>{{ $daysLeft }} días</strong>
                                        @else
                                            - Finaliza hoy!
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> 
                            No hay rondas configuradas para este hackathon todavía.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar con info adicional -->
        <div class="col-md-4">
            <!-- Sección de Jurado -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Jurado</h5>
                </div>
                <div class="card-body">
                    @if(isset($hackathon->judges) && $hackathon->judges->count() > 0)
                        @foreach($hackathon->judges as $judge)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; overflow: hidden;">
                                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed={{ $judge->id }}" class="rounded-circle" width="40" height="40" alt="Jurado">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $judge->name }}</h6>
                                    <span class="text-muted small">{{ $judge->pivot->is_lead_judge ? 'Jurado principal' : 'Jurado' }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Aún no se han asignado jurados para este hackathon.</p>
                    @endif
                </div>
            </div>
            
            <!-- Sección de Organizadores -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Organizadores</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; overflow: hidden;">
                            <img src="https://api.dicebear.com/7.x/adventurer/svg?seed={{ $hackathon->created_by ?? 'admin' }}" class="rounded-circle" width="50" height="50" alt="Organizador">
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $hackathon->creator ? $hackathon->creator->name : 'Administrador' }}</h6>
                            <span class="text-muted small">Organizador principal</span>
                        </div>
                    </div>
                    
                    @if(isset($hackathon->collaborators) && $hackathon->collaborators->count() > 0)
                        @foreach($hackathon->collaborators as $collaborator)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; overflow: hidden;">
                                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed={{ $collaborator->id ?? 'collab' . $loop->index }}" class="rounded-circle" width="40" height="40" alt="Colaborador">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $collaborator->name }}</h6>
                                    <span class="text-muted small">Colaborador</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            
            <!-- Sección de Equipos Participantes -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Equipos Participantes</h5>
                    <span class="badge bg-primary">{{ $hackathon->teams()->count() }}</span>
                </div>
                <div class="card-body">
                    @if($hackathon->teams()->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($hackathon->teams()->limit(5)->get() as $listTeam)
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; overflow: hidden;">
                                            <img src="https://api.dicebear.com/7.x/initials/svg?seed={{ urlencode($listTeam->name) }}" class="rounded-circle" width="36" height="36" alt="{{ $listTeam->name }}">
                                        </div>
                                        <div>
                                            {{ $listTeam->name }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge bg-light text-dark">{{ $listTeam->members()->count() }} miembros</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($hackathon->teams()->count() > 5)
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-sm btn-outline-primary">Ver todos los equipos</a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">No hay equipos participando en este hackathon todavía.</p>
                    @endif
                </div>
            </div>
            
            <!-- Widget de Reglas y Requisitos -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Reglas y Requisitos</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <i class="fas fa-users text-primary me-2"></i>
                            <strong>Equipos:</strong> Máximo {{ $hackathon->team_size }} participantes por equipo
                        </li>
                        <li class="list-group-item px-0">
                            <i class="fas fa-code-branch text-primary me-2"></i>
                            <strong>Código:</strong> Todo el código debe ser original y creado durante el hackathon
                        </li>
                        <li class="list-group-item px-0">
                            <i class="fas fa-calendar-check text-primary me-2"></i>
                            <strong>Entregas:</strong> Subir antes de la fecha límite de cada ronda
                        </li>
                        <li class="list-group-item px-0">
                            <i class="fas fa-copyright text-primary me-2"></i>
                            <strong>Licencia:</strong> Código abierto con licencia MIT o similar
                        </li>
                        <li class="list-group-item px-0">
                            <i class="fas fa-trophy text-primary me-2"></i>
                            <strong>Evaluación:</strong> Innovación, calidad técnica, presentación y utilidad
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Acciones -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    @if($team)
                        <a href="{{ route('student.hackathons.team', ['id' => $team->id]) }}" class="btn btn-primary d-block mb-3">
                            <i class="fas fa-users me-2"></i> Ir a mi equipo
                        </a>
                    @elseif($hackathon->status === 'active')
                        <a href="{{ route('student.hackathons.join', ['id' => $hackathon->id]) }}" class="btn btn-success d-block mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i> Unirme al Hackathon
                        </a>
                    @endif
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('student.hackathons.index') }}" class="btn btn-outline-secondary d-block">
                            <i class="fas fa-arrow-left me-1"></i> Volver a hackathones
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hackathon-header-image {
        max-height: 300px;
        width: 100%;
        object-fit: cover;
    }
    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
</style>
@endsection 