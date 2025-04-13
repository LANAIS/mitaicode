@extends('layouts.app')

@section('title', 'Equipo ' . $team->name)

@section('header', 'Equipo ' . $team->name)

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
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.index') }}">Hackathones</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.show', $hackathon->hackathon_id) }}">{{ $hackathon->title }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.teams', $hackathon->hackathon_id) }}">Equipos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $team->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end">
            @if(Auth::id() === $hackathon->created_by || Auth::user()->role === 'admin' || $isTeamMember)
                <div class="btn-group">
                    @if($isTeamMember)
                        <a href="{{ route('hackathons.team.chat', $team->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-comments"></i> Chat del equipo
                        </a>
                    @endif
                    @if(Auth::id() === $hackathon->created_by || Auth::user()->role === 'admin')
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeTeamModal">
                            <i class="fas fa-trash"></i> Eliminar equipo
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Información del Equipo</h5>
                    <div class="team-avatar d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 40px; height: 40px;">
                        <span class="fw-bold text-secondary">{{ substr($team->name, 0, 2) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>{{ $team->name }}</h5>
                        <p class="text-muted">{{ $team->description }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="text-muted">Creado:</span>
                                <strong>{{ $team->created_at->format('d/m/Y') }}</strong>
                            </div>
                            <div>
                                <span class="text-muted">Miembros:</span>
                                <strong>{{ count($team->members) }} / {{ $hackathon->team_size }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    @if($team->project_name || $team->project_description)
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Proyecto</h6>
                            <p class="mb-1"><strong>{{ $team->project_name }}</strong></p>
                            <p class="mb-0 small">{{ $team->project_description }}</p>
                        </div>
                    @endif
                    
                    @if($isTeamLeader)
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editTeamModal">
                                <i class="fas fa-edit"></i> Editar equipo
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Miembros del Equipo</h5>
                    <span class="badge bg-info">{{ count($team->members) }}</span>
                </div>
                <div class="card-body">
                    @foreach($team->members as $member)
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $member->profile_photo_url ?? 'https://via.placeholder.com/48' }}" class="rounded-circle me-3" width="48" alt="{{ $member->name }}">
                            <div>
                                <h6 class="mb-0">{{ $member->name }}</h6>
                                <small class="text-muted">{{ $member->email }}</small>
                                <br>
                                @if($member->pivot->is_leader)
                                    <span class="badge bg-primary">Líder</span>
                                @else
                                    <span class="badge bg-secondary">Miembro</span>
                                @endif
                            </div>
                            @if($isTeamLeader && !$member->pivot->is_leader && $hackathon->allow_team_changes)
                                <div class="ms-auto">
                                    <form action="{{ route('hackathons.team.remove-member', [$team->id, $member->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar a este miembro del equipo?')">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    
                    @if($isTeamLeader && count($team->members) < $hackathon->team_size && $hackathon->allow_team_changes)
                        <hr>
                        <div class="mt-3">
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#inviteModal">
                                <i class="fas fa-user-plus"></i> Invitar miembro
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Progreso y rondas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Progreso del Hackathon</h5>
                </div>
                <div class="card-body">
                    @if(isset($hackathon->rounds) && $hackathon->rounds->count() > 0)
                        <div class="progress mb-4" style="height: 25px;">
                            @php
                                $completedRounds = $teamDeliverables->count();
                                $totalRounds = $hackathon->rounds->count();
                                $percentage = $totalRounds > 0 ? ($completedRounds / $totalRounds) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ round($percentage) }}% completado
                            </div>
                        </div>
                        
                        <ul class="list-group">
                            @foreach($hackathon->rounds->sortBy('start_date') as $round)
                                @php
                                    $deliverable = $teamDeliverables->where('round_id', $round->id)->first();
                                    $isCompleted = !is_null($deliverable);
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $round->name }}</h6>
                                        <small class="text-muted">
                                            {{ $round->start_date->format('d/m/Y') }} - {{ $round->end_date->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    @if($isCompleted)
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success me-2">Completado</span>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewDeliverableModal{{ $round->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    @else
                                        @if($round->isCurrentlyActive())
                                            @if($isTeamMember)
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#submitDeliverableModal{{ $round->id }}">
                                                    <i class="fas fa-upload"></i> Entregar
                                                </button>
                                            @else
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            @endif
                                        @else
                                            @if($round->end_date->isPast())
                                                <span class="badge bg-danger">No entregado</span>
                                            @else
                                                <span class="badge bg-secondary">Próximamente</span>
                                            @endif
                                        @endif
                                    @endif
                                </li>
                                
                                @if($isCompleted)
                                    <!-- Modal para ver entregable -->
                                    <div class="modal fade" id="viewDeliverableModal{{ $round->id }}" tabindex="-1" aria-labelledby="viewDeliverableModalLabel{{ $round->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewDeliverableModalLabel{{ $round->id }}">Entregable: {{ $round->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h6>Título</h6>
                                                        <p>{{ $deliverable->title }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Descripción</h6>
                                                        <p>{{ $deliverable->description }}</p>
                                                    </div>
                                                    @if($deliverable->link)
                                                        <div class="mb-3">
                                                            <h6>Enlace</h6>
                                                            <a href="{{ $deliverable->link }}" target="_blank">{{ $deliverable->link }}</a>
                                                        </div>
                                                    @endif
                                                    @if($deliverable->file_path)
                                                        <div class="mb-3">
                                                            <h6>Archivo</h6>
                                                            <a href="{{ Storage::url($deliverable->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                                <i class="fas fa-download"></i> Descargar archivo
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <div class="mb-3">
                                                        <h6>Entregado por</h6>
                                                        <p>{{ $deliverable->user->name ?? 'Usuario desconocido' }} - {{ $deliverable->created_at->format('d/m/Y H:i') }}</p>
                                                    </div>
                                                    @if($deliverable->feedback)
                                                        <div class="alert alert-info">
                                                            <h6 class="alert-heading">Feedback del jurado</h6>
                                                            <p class="mb-0">{{ $deliverable->feedback }}</p>
                                                            <hr>
                                                            <small class="mb-0">Calificación: {{ $deliverable->score ?? 'No calificado' }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    @if($isTeamMember && $round->isCurrentlyActive())
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#submitDeliverableModal{{ $round->id }}">
                                                            <i class="fas fa-edit"></i> Editar entrega
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($isTeamMember && $round->isCurrentlyActive())
                                    <!-- Modal para subir entregable -->
                                    <div class="modal fade" id="submitDeliverableModal{{ $round->id }}" tabindex="-1" aria-labelledby="submitDeliverableModalLabel{{ $round->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="submitDeliverableModalLabel{{ $round->id }}">{{ $isCompleted ? 'Editar' : 'Enviar' }} entregable: {{ $round->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('hackathons.team.submit-deliverable', [$team->id, $round->id]) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <h6 class="alert-heading">Requisitos para esta ronda</h6>
                                                            <p class="mb-0">{{ $round->deliverables }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="title" class="form-label">Título</label>
                                                            <input type="text" class="form-control" id="title" name="title" value="{{ $deliverable->title ?? old('title') }}" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Descripción</label>
                                                            <textarea class="form-control" id="description" name="description" rows="4" required>{{ $deliverable->description ?? old('description') }}</textarea>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="link" class="form-label">Enlace (opcional)</label>
                                                            <input type="url" class="form-control" id="link" name="link" value="{{ $deliverable->link ?? old('link') }}">
                                                            <div class="form-text">Enlace a un repositorio, prototipo, presentación, etc.</div>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="file" class="form-label">Archivo (opcional)</label>
                                                            <input type="file" class="form-control" id="file" name="file">
                                                            @if($isCompleted && $deliverable->file_path)
                                                                <div class="form-text">
                                                                    Archivo actual: 
                                                                    <a href="{{ Storage::url($deliverable->file_path) }}" target="_blank">Ver archivo</a>
                                                                    <div class="form-check mt-1">
                                                                        <input class="form-check-input" type="checkbox" id="delete_file" name="delete_file">
                                                                        <label class="form-check-label" for="delete_file">
                                                                            Eliminar archivo existente
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">{{ $isCompleted ? 'Actualizar' : 'Enviar' }} entregable</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No hay rondas configuradas para este hackathon.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Actividad reciente -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actividad Reciente</h5>
                </div>
                <div class="card-body">
                    @if(isset($teamActivities) && count($teamActivities) > 0)
                        <div class="timeline">
                            @foreach($teamActivities as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-item-marker">
                                        <div class="timeline-item-marker-text">{{ $activity->created_at->format('d M') }}</div>
                                        <div class="timeline-item-marker-indicator bg-primary"></div>
                                    </div>
                                    <div class="timeline-item-content">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $activity->user->profile_photo_url ?? 'https://via.placeholder.com/32' }}" class="rounded-circle me-2" width="32" height="32" alt="{{ $activity->user->name }}">
                                            <div>
                                                <strong>{{ $activity->user->name }}</strong>
                                                <span class="ms-1">{{ $activity->description }}</span>
                                                <div class="text-muted small">{{ $activity->created_at->format('H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No hay actividad reciente para este equipo.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para invitar miembros -->
@if($isTeamLeader && $hackathon->allow_team_changes)
    <div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inviteModalLabel">Invitar Miembros</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('hackathons.team.invite', $team->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="form-text">Ingresa el correo electrónico del usuario que deseas invitar al equipo.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Mensaje (Opcional)</label>
                            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar invitación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Modal para editar equipo -->
@if($isTeamLeader)
    <div class="modal fade" id="editTeamModal" tabindex="-1" aria-labelledby="editTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTeamModalLabel">Editar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('hackathons.team.update', $team->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del equipo</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $team->name }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control" id="description" name="description" rows="2">{{ $team->description }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="project_name" class="form-label">Nombre del proyecto (Opcional)</label>
                            <input type="text" class="form-control" id="project_name" name="project_name" value="{{ $team->project_name }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="project_description" class="form-label">Descripción del proyecto (Opcional)</label>
                            <textarea class="form-control" id="project_description" name="project_description" rows="3">{{ $team->project_description }}</textarea>
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

<!-- Modal para eliminar equipo -->
@if(Auth::id() === $hackathon->created_by || Auth::user()->role === 'admin')
    <div class="modal fade" id="removeTeamModal" tabindex="-1" aria-labelledby="removeTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeTeamModalLabel">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el equipo <strong>{{ $team->name }}</strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer y eliminará todos los entregables y mensajes del equipo.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('hackathons.teams.destroy', [$hackathon->id, $team->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    .timeline {
        position: relative;
        padding-left: 1rem;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0.25rem;
        height: 100%;
        border-left: 1px solid #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    
    .timeline-item-marker {
        position: absolute;
        left: -1rem;
        width: 1rem;
    }
    
    .timeline-item-marker-text {
        position: absolute;
        width: 100px;
        left: -50px;
        text-align: right;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
    }
    
    .timeline-item-marker-indicator {
        display: block;
        width: 10px;
        height: 10px;
        border-radius: 100%;
        margin-top: 0.25rem;
        margin-left: -0.25rem;
    }
    
    .timeline-item-content {
        padding-left: 1rem;
        padding-bottom: 1rem;
    }
</style>
@endsection 