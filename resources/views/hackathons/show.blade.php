@extends('layouts.app')

@section('title', $hackathon->title)

@section('header', $hackathon->title)

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
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.index') }}">Hackathones</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $hackathon->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            @if(Auth::id() === $hackathon->created_by || Auth::user()->role === 'admin')
                <a href="{{ route('hackathons.edit', $hackathon->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Editar Hackathon
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Información del Hackathon</h5>
                    <span class="badge bg-{{ $hackathon->status === 'active' ? 'success' : ($hackathon->status === 'pending' ? 'warning' : 'secondary') }}">
                        {{ $hackathon->status === 'active' ? 'Activo' : ($hackathon->status === 'pending' ? 'Próximo' : 'Finalizado') }}
                    </span>
                </div>
                
                @if($hackathon->image)
                    <img src="{{ Storage::url($hackathon->image) }}" class="card-img-top hackathon-header-image" alt="{{ $hackathon->title }}">
                @endif
                
                <div class="card-body">
                    @if(auth()->user()->id === $hackathon->created_by || auth()->user()->role === 'admin')
                        <div class="card bg-light mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-cogs"></i> Panel de Control</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="d-grid">
                                            <form action="{{ route('hackathons.toggle.registration', $hackathon->id) }}" method="POST" class="me-1">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-{{ $hackathon->status === 'active' ? 'warning' : 'success' }} btn-lg w-100">
                                                    <i class="fas fa-{{ $hackathon->status === 'active' ? 'lock' : 'unlock' }} me-2"></i> 
                                                    {{ $hackathon->status === 'active' ? 'Cerrar Inscripciones' : 'Abrir Inscripciones' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-grid">
                                            <form action="{{ route('hackathons.advance.round', $hackathon->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-info btn-lg w-100">
                                                    <i class="fas fa-forward me-2"></i> Avanzar a Siguiente Fase
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="mb-2 mt-1">Cambiar estado del Hackathon:</h6>
                                        <div class="d-flex gap-2 mb-3">
                                            <form action="{{ route('hackathons.update.status', $hackathon->id) }}" method="POST" class="flex-grow-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="btn btn-{{ $hackathon->status === 'pending' ? 'secondary disabled' : 'warning' }} w-100">
                                                    <i class="fas fa-hourglass-start me-1"></i> Próximo
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('hackathons.update.status', $hackathon->id) }}" method="POST" class="flex-grow-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="btn btn-{{ $hackathon->status === 'active' ? 'secondary disabled' : 'primary' }} w-100">
                                                    <i class="fas fa-play-circle me-1"></i> Activo
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('hackathons.update.status', $hackathon->id) }}" method="POST" class="flex-grow-1">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="finished">
                                                <button type="submit" class="btn btn-{{ $hackathon->status === 'finished' ? 'secondary disabled' : 'danger' }} w-100">
                                                    <i class="fas fa-flag-checkered me-1"></i> Finalizado
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <strong>Estado actual:</strong> 
                                            <span class="badge bg-{{ $hackathon->status === 'active' ? 'success' : ($hackathon->status === 'pending' ? 'warning' : 'secondary') }} ms-2">
                                                {{ $hackathon->status === 'active' ? 'Inscripciones Abiertas' : ($hackathon->status === 'pending' ? 'Inscripciones No Iniciadas' : 'Inscripciones Cerradas') }}
                                            </span>
                                            @if(isset($hackathon->rounds) && $hackathon->rounds->where('is_active', true)->count() > 0)
                                                @php
                                                    $activeRound = $hackathon->rounds->where('is_active', true)->first();
                                                @endphp
                                                <span class="ms-3">
                                                    <strong>Fase actual:</strong> 
                                                    <span class="badge bg-success ms-2">{{ $activeRound->name }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="mb-2">Acciones Adicionales:</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('hackathons.edit', $hackathon->id) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-edit me-1"></i> Editar detalles
                                            </a>
                                            <a href="{{ route('hackathons.rounds.index', $hackathon->id) }}" class="btn btn-outline-info">
                                                <i class="fas fa-list-ol me-1"></i> Gestionar rondas
                                            </a>
                                            <a href="{{ route('hackathons.judges', $hackathon->id) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-gavel me-1"></i> Gestionar jurados
                                            </a>
                                            @if(isset($hackathon->teams_count) && $hackathon->teams_count > 0)
                                            <a href="{{ route('hackathons.teams', $hackathon->id) }}" class="btn btn-outline-success">
                                                <i class="fas fa-users me-1"></i> Ver equipos
                                            </a>
                                            @endif
                                            <a href="{{ route('hackathons.deliverables.evaluate', $hackathon->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-clipboard-check me-1"></i> Evaluar entregables
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteHackathonModal">
                                                <i class="fas fa-trash me-1"></i> Eliminar hackathon
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="hackathon-dates mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="text-muted">Inicio:</span>
                                <strong>{{ $hackathon->start_date ? \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') : 'Fecha por definir' }}</strong>
                            </div>
                            <div>
                                <span class="text-muted">Fin:</span>
                                <strong>{{ $hackathon->end_date ? \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') : 'Fecha por definir' }}</strong>
                            </div>
                            <div>
                                <span class="text-muted">Duración:</span>
                                <strong>
                                    @if($hackathon->start_date && $hackathon->end_date)
                                        {{ \Carbon\Carbon::parse($hackathon->start_date)->diffInDays(\Carbon\Carbon::parse($hackathon->end_date)) }} días
                                    @else
                                        Por definir
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hackathon-description mb-4">
                        <h6>Descripción</h6>
                        <p class="card-text">{{ $hackathon->description }}</p>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Equipos</h6>
                                    <p class="card-text fs-4">{{ $hackathon->teams_count ?? '0' }} / {{ $hackathon->max_teams }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Participantes</h6>
                                    <p class="card-text fs-4">{{ $hackathon->participants_count ?? '0' }} / {{ $hackathon->max_participants }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Tamaño de equipos</h6>
                                    <p class="card-text fs-4">{{ $hackathon->team_size }} máx.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información para participantes -->
                    @if(!$isUserJudge && !Auth::user()->role === 'admin' && Auth::id() !== $hackathon->created_by)
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Información para participantes</h6>
                                    @if($userTeam)
                                        <p class="mb-1">Estás participando con el equipo <strong>{{ $userTeam->name }}</strong>.</p>
                                        <a href="{{ route('student.hackathons.team', $userTeam->id) }}" class="btn btn-sm btn-primary mt-2">Ver mi equipo</a>
                                    @else
                                        <p class="mb-1">Aún no estás participando en este hackathon.</p>
                                        @if($hackathon->status === 'active')
                                            @if(isset($hackathon->id))
                                            <a href="{{ route('student.hackathons.join', $hackathon->id) }}" class="btn btn-sm btn-primary mt-2">Unirse al Hackathon</a>
                                            @else
                                            <button class="btn btn-sm btn-secondary mt-2" disabled>ID no disponible</button>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-footer bg-transparent">
                    @if(auth()->user()->id !== $hackathon->created_by && auth()->user()->role !== 'admin')
                        @if($hackathon->status === 'active')
                            @if($userTeam)
                                <a href="{{ route('student.hackathons.team', $userTeam->id) }}" class="btn btn-primary">Ver mi equipo</a>
                            @else
                                @if(isset($hackathon->id))
                                <a href="{{ route('student.hackathons.join', $hackathon->id) }}" class="btn btn-primary">Unirse al Hackathon</a>
                                @else
                                <button class="btn btn-secondary" disabled>ID no disponible</button>
                                @endif
                            @endif
                        @elseif($hackathon->status === 'pending')
                            <button class="btn btn-outline-secondary" disabled>Inscripciones cerradas - Próximamente</button>
                        @elseif($hackathon->status === 'closed')
                            <button class="btn btn-outline-secondary" disabled>Inscripciones cerradas - En curso</button>
                        @else
                            <button class="btn btn-outline-secondary" disabled>Hackathon finalizado</button>
                        @endif
                    @endif
                </div>
            </div>
            
            <!-- Sección de Rondas con detalles -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rondas y Fases del Hackathon</h5>
                    <span class="badge bg-info">{{ $hackathon->rounds_count ?? '0' }} rondas</span>
                </div>
                <div class="card-body">
                    @if(isset($hackathon->rounds) && $hackathon->rounds->count() > 0)
                        <div class="accordion" id="accordionRounds">
                            @foreach($hackathon->rounds as $round)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $round->id }}">
                                        <button class="accordion-button {{ $round->is_active ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $round->id }}" aria-expanded="{{ $round->is_active ? 'true' : 'false' }}" aria-controls="collapse{{ $round->id }}">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $round->name }}</strong>
                                                    <span class="ms-2 badge bg-{{ $round->hasEnded() ? 'secondary' : ($round->isCurrentlyActive() ? 'success' : 'warning') }}">
                                                        {{ $round->hasEnded() ? 'Finalizada' : ($round->isCurrentlyActive() ? 'En curso' : 'Pendiente') }}
                                                    </span>
                                                </div>
                                                <small>{{ $round->start_date->format('d/m/Y') }} - {{ $round->end_date->format('d/m/Y') }}</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $round->id }}" class="accordion-collapse collapse {{ $round->is_active ? 'show' : '' }}" aria-labelledby="heading{{ $round->id }}" data-bs-parent="#accordionRounds">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="mb-2">Objetivos</h6>
                                                    <p>{{ $round->objectives ?: 'No se han definido objetivos para esta ronda.' }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-2">Entregables esperados</h6>
                                                    <p>{{ $round->deliverables ?: 'No se han definido entregables para esta ronda.' }}</p>
                                                </div>
                                            </div>
                                            
                                            @if($round->description)
                                                <h6 class="mb-2 mt-3">Detalles adicionales</h6>
                                                <p>{{ $round->description }}</p>
                                            @endif
                                            
                                            @if($userTeam && $round->isCurrentlyActive())
                                                <div class="mt-3">
                                                    <a href="#" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-upload"></i> Subir entregable para esta ronda
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">No hay rondas configuradas para este hackathon.</p>
                            @if(Auth::id() === $hackathon->created_by || Auth::user()->role === 'admin')
                                <a href="{{ route('hackathons.rounds.index', $hackathon->id) }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Añadir rondas
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Información sobre la ronda actual -->
            @if(isset($hackathon->rounds) && $hackathon->rounds->where('is_active', true)->count() > 0)
                @php
                    $activeRound = $hackathon->rounds->where('is_active', true)->first();
                @endphp
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Ronda Actual: {{ $activeRound->name }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="text-muted">Inicio:</span>
                                <strong>{{ $activeRound->start_date->format('d/m/Y') }}</strong>
                            </div>
                            <div>
                                <span class="text-muted">Fin:</span>
                                <strong>{{ $activeRound->end_date->format('d/m/Y') }}</strong>
                            </div>
                        </div>
                        
                        <div class="progress mb-3" style="height: 20px;">
                            @php
                                $totalDays = $activeRound->start_date->diffInDays($activeRound->end_date);
                                $passedDays = $activeRound->start_date->diffInDays(now());
                                $percentage = min(100, max(0, ($passedDays / $totalDays) * 100));
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">{{ round($percentage) }}%</div>
                        </div>
                        
                        @if($activeRound->isCurrentlyActive())
                            <div class="alert alert-success">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Tiempo restante:</h6>
                                        <strong>{{ now()->diffInDays($activeRound->end_date) }} días</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if($userTeam && $activeRound->isCurrentlyActive())
                            <a href="#" class="btn btn-primary btn-block mt-3">
                                <i class="fas fa-upload"></i> Subir entregable
                            </a>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Panel de jurados -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Equipo Evaluador</h5>
                </div>
                <div class="card-body">
                    @if(isset($hackathon->judges) && $hackathon->judges->count() > 0)
                        @foreach($hackathon->judges as $judge)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar me-3">
                                    <img src="{{ $judge->profile_photo_url ?? 'https://via.placeholder.com/40' }}" class="rounded-circle" width="40" alt="Jurado">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $judge->name }}</h6>
                                    <span class="text-muted small">{{ $judge->pivot->is_lead_judge ? 'Jurado principal' : 'Jurado' }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Aún no se han asignado jurados para este hackathon.</p>
                        @if(Auth::id() === $hackathon->created_by || Auth::user()->role === 'admin')
                            <a href="{{ route('hackathons.judges', $hackathon->id) }}" class="btn btn-sm btn-outline-primary mt-3">
                                <i class="fas fa-plus"></i> Asignar jurados
                            </a>
                        @endif
                    @endif
                </div>
            </div>
            
            <!-- Panel de equipo organizador -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Equipo Organizador</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar me-3">
                            <img src="{{ $hackathon->creator ? $hackathon->creator->profile_photo_url ?? 'https://via.placeholder.com/50' : 'https://via.placeholder.com/50' }}" class="rounded-circle" width="50" alt="Organizador">
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $hackathon->creator ? $hackathon->creator->name : 'Administrador' }}</h6>
                            <span class="text-muted small">Organizador principal</span>
                        </div>
                    </div>
                    
                    @if(isset($hackathon->collaborators) && $hackathon->collaborators->count() > 0)
                        @foreach($hackathon->collaborators as $collaborator)
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-3">
                                    <img src="{{ $collaborator->profile_photo_url ?? 'https://via.placeholder.com/40' }}" class="rounded-circle" width="40" alt="Colaborador">
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
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="deleteHackathonModal" tabindex="-1" aria-labelledby="deleteHackathonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteHackathonModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-warning fa-4x"></i>
                </div>
                <p>¿Estás seguro de que deseas eliminar el hackathon <strong>{{ $hackathon->title }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-1"></i> Esta acción también eliminará:
                    <ul class="mb-0 mt-1">
                        <li>Todos los equipos registrados</li>
                        <li>Todas las rondas configuradas</li>
                        <li>Todos los entregables subidos</li>
                        <li>Todas las evaluaciones realizadas</li>
                    </ul>
                    <p class="mt-2 mb-0"><strong>Esta acción no se puede deshacer.</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('hackathons.destroy', $hackathon->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Eliminar definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .hackathon-header-image {
        height: 200px;
        object-fit: cover;
    }
    
    .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    
    .accordion-button.collapsed::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
</style>
@endsection 