@extends('layouts.app')

@section('title', 'Hackathones')

@section('content')
<div class="dashboard-header">
    <h1>Hackathones</h1>
    <p class="text-muted">Participa en competencias de innovación y crea soluciones usando IA y programación</p>
</div>

<!-- Pestañas de Hackathones -->
<ul class="nav nav-tabs mb-4" id="hackathonsStudentTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="active-hackathons-tab" data-bs-toggle="tab" data-bs-target="#active-hackathons" type="button" role="tab" aria-controls="active-hackathons" aria-selected="true">
            Activos <span class="badge bg-primary ms-1">{{ count($activeHackathons) }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="my-teams-tab" data-bs-toggle="tab" data-bs-target="#my-teams" type="button" role="tab" aria-controls="my-teams" aria-selected="false">
            Mis Equipos <span class="badge bg-success ms-1">{{ count($myTeams) }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="past-hackathons-tab" data-bs-toggle="tab" data-bs-target="#past-hackathons" type="button" role="tab" aria-controls="past-hackathons" aria-selected="false">
            Anteriores <span class="badge bg-secondary ms-1">{{ count($pastHackathons) }}</span>
        </button>
    </li>
</ul>

<!-- Contenido de las pestañas -->
<div class="tab-content" id="hackathonsTabContent">
    <!-- Hackathones Activos -->
    <div class="tab-pane fade show active" id="active-hackathons" role="tabpanel" aria-labelledby="active-hackathons-tab">
        <div class="row g-4">
            @forelse($activeHackathons as $hackathon)
                <div class="col-lg-6">
                    <div class="card h-100 hackathon-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="m-0">{{ $hackathon->title }}</h5>
                            <span class="badge bg-{{ $hackathon->current_round > 1 ? 'success' : 'primary' }}">
                                En progreso - Ronda {{ $hackathon->current_round }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                @foreach($hackathon->tags as $tag)
                                <span class="badge rounded-pill bg-primary me-2">{{ $tag }}</span>
                                @endforeach
                            </div>
                            
                            <div class="hackathon-info mb-3">
                                <div><i class="fas fa-calendar me-2"></i> <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($hackathon->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($hackathon->end_date)->format('d M, Y') }}</div>
                                <div><i class="fas fa-users me-2"></i> <strong>Equipos:</strong> {{ $hackathon->team_count }} equipos participando</div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-layer-group me-2"></i> <strong>Progreso:</strong>
                                    <div class="progress ms-2" style="height: 8px; width: 150px;">
                                        @php
                                            $totalRounds = 3;
                                            $progress = ($hackathon->current_round / $totalRounds) * 100;
                                        @endphp
                                        <div class="progress-bar progress-bar-striped progress-bar-animated {{ $hackathon->current_round > 1 ? 'bg-success' : '' }}" 
                                             role="progressbar" 
                                             style="width: {{ $progress }}%" 
                                             aria-valuenow="{{ $progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                    <span class="ms-2">Ronda {{ $hackathon->current_round }} de {{ $totalRounds }}</span>
                                </div>
                            </div>
                            
                            <p class="card-text">{{ $hackathon->description }}</p>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($hackathon->is_participating ?? false)
                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i> Ya estás participando</span>
                                    @else
                                        <span class="text-danger"><i class="fas fa-info-circle me-1"></i> No estás participando</span>
                                    @endif
                                </div>
                                @if($hackathon->is_participating ?? false)
                                    <a href="{{ route('student.hackathons.team', $hackathon->user_team->team_id) }}" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i> Ver mi equipo
                                    </a>
                                @else
                                    <a href="{{ route('student.hackathons.join', $hackathon->hackathon_id) }}" class="btn btn-success">
                                        <i class="fas fa-plus me-1"></i> Unirse o crear equipo
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No hay hackathones activos en este momento. ¡Revisa más tarde!
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Mis Equipos -->
    <div class="tab-pane fade" id="my-teams" role="tabpanel" aria-labelledby="my-teams-tab">
        @forelse($myTeams as $team)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $team->team_name }}</h5>
                    <span class="badge bg-success">Hackathon: {{ $team->hackathon->title }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <h6 class="fw-bold">Miembros del equipo</h6>
                            <ul class="list-group mb-3">
                                @foreach($team->members as $member)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $member->profile_image ?? asset('img/avatars/default.png') }}" class="rounded-circle me-2" width="30" height="30" alt="Avatar">
                                            <span>{{ $member->name }} {{ $member->user_id == Auth::id() ? '(Tú)' : '' }}</span>
                                        </div>
                                        @if($member->user_id == $team->leader_id)
                                            <span class="badge bg-primary rounded-pill">Líder</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('student.hackathons.team.chat', $team->team_id) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-comment-dots me-1"></i> Chat de equipo
                            </a>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Ronda actual: {{ $team->hackathon->current_round_name }}</h6>
                                <div>
                                    @php
                                        $daysRemaining = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($team->hackathon->current_round_end_date), false);
                                    @endphp
                                    <span class="badge bg-{{ $daysRemaining <= 2 ? 'warning' : 'info' }}">
                                        Tiempo restante: {{ $daysRemaining }} días
                                    </span>
                                </div>
                            </div>
                            
                            <div class="objectives-card mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Objetivos de la ronda</h6>
                                    </div>
                                    <div class="card-body">
                                        {!! $team->hackathon->current_round_objectives !!}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="deliverables-card mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Entregables</h6>
                                    </div>
                                    <div class="card-body">
                                        {!! $team->hackathon->current_round_deliverables !!}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-column flex-sm-row justify-content-between">
                                <a href="{{ route('student.hackathons.team.submit', $team->team_id) }}" class="btn btn-primary mb-2 mb-sm-0">
                                    <i class="fas fa-upload me-1"></i> Subir entregables
                                </a>
                                @if($team->repository_url)
                                    <a href="{{ $team->repository_url }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-code-branch me-1"></i> Ver repositorio del proyecto
                                    </a>
                                @else
                                    <a href="{{ route('student.hackathons.team.edit', $team->team_id) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Configurar equipo
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                No estás participando en ningún equipo de hackathon actualmente. ¡Únete a uno para comenzar!
            </div>
        @endforelse
    </div>
    
    <!-- Hackathones Anteriores -->
    <div class="tab-pane fade" id="past-hackathons" role="tabpanel" aria-labelledby="past-hackathons-tab">
        @forelse($pastHackathons as $hackathon)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $hackathon->title }}</h5>
                    <span class="badge bg-secondary">Finalizado</span>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row">
                        @if(isset($hackathon->team_info))
                            <div class="me-md-4 mb-3 mb-md-0 text-center">
                                <div class="achievement-badge p-3 mb-2">
                                    @php
                                        $position = $hackathon->team_info->position ?? 0;
                                        $icon = 'trophy';
                                        $color = 'warning';
                                        
                                        if ($position > 3) {
                                            $icon = 'award';
                                            $color = 'secondary';
                                        } elseif ($position == 3) {
                                            $icon = 'medal';
                                            $color = 'danger';
                                        } elseif ($position == 2) {
                                            $icon = 'medal';
                                            $color = 'warning';
                                        }
                                    @endphp
                                    <i class="fas fa-{{ $icon }} text-{{ $color }} fa-4x"></i>
                                </div>
                                <h5>{{ $position }}º Puesto</h5>
                            </div>
                        @endif
                        
                        <div class="flex-grow-1">
                            <div class="hackathon-info mb-3">
                                <div><i class="fas fa-calendar me-2"></i> <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($hackathon->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($hackathon->end_date)->format('d M, Y') }}</div>
                                
                                @if(isset($hackathon->team_info))
                                    <div>
                                        <i class="fas fa-users me-2"></i> <strong>Equipo:</strong> 
                                        {{ $hackathon->team_info->team_name }} 
                                        ({{ implode(', ', $hackathon->team_members->pluck('first_name')->toArray()) }})
                                    </div>
                                    <div>
                                        <i class="fas fa-project-diagram me-2"></i> <strong>Proyecto:</strong> 
                                        {{ $hackathon->final_submission->project_name ?? 'No disponible' }}
                                    </div>
                                @endif
                            </div>
                            
                            <p>
                                {{ $hackathon->final_submission->description ?? 'No hay descripción disponible del proyecto.' }}
                            </p>
                            
                            @if(isset($hackathon->team_info) && isset($hackathon->team_info->achievements))
                                <div class="project-achievements mb-3">
                                    <h6 class="fw-bold">Logros destacados:</h6>
                                    <ul>
                                        @foreach($hackathon->team_info->achievements as $achievement)
                                            <li>{{ $achievement }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div class="d-flex flex-column flex-sm-row">
                                @if(isset($hackathon->final_submission))
                                    <a href="{{ route('student.hackathons.project', $hackathon->final_submission->submission_id) }}" class="btn btn-outline-primary me-sm-2 mb-2 mb-sm-0">
                                        <i class="fas fa-eye me-1"></i> Ver proyecto final
                                    </a>
                                @endif
                                @if(isset($hackathon->team_info))
                                    <a href="{{ route('student.hackathons.certificate', $hackathon->team_info->team_id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-award me-1"></i> Ver certificado
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                No has participado en hackathones anteriores. ¡Únete a uno activo para comenzar tu experiencia!
            </div>
        @endforelse
    </div>
</div>

@endsection

@section('styles')
<style>
.hackathon-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.hackathon-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.achievement-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #4e73df, #36b9cc);
    color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
    margin: 10px;
    transition: all 0.3s ease;
}

.achievement-badge:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Código JavaScript para manejar eventos en la página de hackathones
    });
</script>
@endsection 