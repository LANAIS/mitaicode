@extends('layouts.app')

@section('title', 'Hackathones')

@section('header', 'Hackathones')

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
            <h1 class="h3">Hackathones disponibles</h1>
            <p class="text-muted">Participa en competencias de innovación y crea soluciones usando IA y programación</p>
        </div>
    </div>

    <!-- Pestañas de Hackathones -->
    <ul class="nav nav-tabs mb-4" id="hackathonsStudentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="active-hackathons-tab" data-bs-toggle="tab" data-bs-target="#active-hackathons" 
                    type="button" role="tab" aria-controls="active-hackathons" aria-selected="true">
                Activos <span class="badge bg-primary ms-1">{{ count($activeHackathons) }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="my-teams-tab" data-bs-toggle="tab" data-bs-target="#my-teams" 
                    type="button" role="tab" aria-controls="my-teams" aria-selected="false">
                Mis Equipos <span class="badge bg-success ms-1">{{ count($myTeams) }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="past-hackathons-tab" data-bs-toggle="tab" data-bs-target="#past-hackathons" 
                    type="button" role="tab" aria-controls="past-hackathons" aria-selected="false">
                Anteriores <span class="badge bg-secondary ms-1">{{ count($pastHackathons) }}</span>
            </button>
        </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="hackathonsStudentTabsContent">
        <!-- Hackathones Activos -->
        <div class="tab-pane fade show active" id="active-hackathons" role="tabpanel" aria-labelledby="active-hackathons-tab">
            @if($activeHackathons->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    @foreach($activeHackathons as $hackathon)
                        <div class="col">
                            <div class="card h-100 hackathon-card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="m-0">{{ $hackathon->title }}</h5>
                                    <span class="badge bg-success">Activo</span>
                                </div>
                                @if($hackathon->image)
                                    <img src="{{ Storage::url($hackathon->image) }}" class="card-img-top hackathon-image" alt="{{ $hackathon->title }}">
                                @else
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light hackathon-image">
                                        <i class="fas fa-code fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <p class="card-text">{{ Str::limit($hackathon->description, 150) }}</p>
                                    
                                    <div class="hackathon-info mb-3">
                                        <div><i class="fas fa-calendar me-2"></i> <strong>Fecha:</strong> 
                                            {{ \Carbon\Carbon::parse($hackathon->start_date)->format('d M') }} - 
                                            {{ \Carbon\Carbon::parse($hackathon->end_date)->format('d M, Y') }}
                                        </div>
                                        <div><i class="fas fa-users me-2"></i> <strong>Equipos:</strong> 
                                            {{ $hackathon->teams_count }} de {{ $hackathon->max_teams }} equipos
                                        </div>
                                        <div><i class="fas fa-layer-group me-2"></i> <strong>Rondas:</strong> 
                                            {{ $hackathon->rounds_count }} rondas
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($hackathon->is_participating)
                                                <span class="text-success"><i class="fas fa-check-circle me-1"></i> Ya estás participando</span>
                                            @endif
                                        </div>
                                        <div>
                                            @if($hackathon->is_participating)
                                                <a href="{{ route('student.hackathons.team', ['id' => $hackathon->team->id]) }}" class="btn btn-primary">
                                                    <i class="fas fa-users me-1"></i> Ver mi equipo
                                                </a>
                                            @else
                                                <a href="{{ route('student.hackathons.details', ['id' => $hackathon->id]) }}" class="btn btn-outline-primary me-2">
                                                    <i class="fas fa-eye me-1"></i> Ver detalles
                                                </a>
                                                <a href="{{ route('student.hackathons.join', ['id' => $hackathon->id]) }}" class="btn btn-success">
                                                    <i class="fas fa-sign-in-alt me-1"></i> Participar
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No hay hackathones activos en este momento.
                </div>
            @endif
        </div>

        <!-- Mis Equipos -->
        <div class="tab-pane fade" id="my-teams" role="tabpanel" aria-labelledby="my-teams-tab">
            @if($myTeams->count() > 0)
                <div class="row">
                    @foreach($myTeams as $team)
                        <div class="col-md-12 mb-4">
                            <div class="card team-card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $team->name }}</h5>
                                    <span class="badge bg-{{ $team->hackathon->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ $team->hackathon->status === 'active' ? 'Activo' : 'Finalizado' }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-3">{{ $team->hackathon->title }}</h6>
                                            <p>{{ Str::limit($team->description, 200) }}</p>
                                            
                                            <div class="team-info mb-3">
                                                <div><i class="fas fa-calendar me-2"></i> <strong>Hackathon:</strong> 
                                                    {{ \Carbon\Carbon::parse($team->hackathon->start_date)->format('d M') }} - 
                                                    {{ \Carbon\Carbon::parse($team->hackathon->end_date)->format('d M, Y') }}
                                                </div>
                                                <div><i class="fas fa-users me-2"></i> <strong>Miembros:</strong> 
                                                    {{ $team->members->count() }} de {{ $team->hackathon->team_size }} máximo
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="team-members-preview mb-3">
                                                @foreach($team->members->take(4) as $member)
                                                    <div class="avatar rounded-circle mb-2 mx-auto" style="width: 40px; height: 40px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                                        <span>{{ substr($member->user->first_name ?? 'U', 0, 1) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <a href="{{ route('student.hackathons.team', ['id' => $team->id]) }}" class="btn btn-primary w-100 mb-2">
                                                <i class="fas fa-eye me-1"></i> Ver equipo
                                            </a>
                                            
                                            @if($team->hackathon->status === 'active')
                                                <form action="{{ route('student.hackathons.leave', ['id' => $team->hackathon->id]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('¿Estás seguro de querer abandonar este equipo?')">
                                                        <i class="fas fa-sign-out-alt me-1"></i> Abandonar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No estás participando en ningún hackathon actualmente.
                    <p class="mt-2 mb-0">Explora los hackathones activos y únete a uno para empezar a participar.</p>
                </div>
            @endif
        </div>

        <!-- Hackathones Anteriores -->
        <div class="tab-pane fade" id="past-hackathons" role="tabpanel" aria-labelledby="past-hackathons-tab">
            @if($pastHackathons->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    @foreach($pastHackathons as $hackathon)
                        <div class="col">
                            <div class="card h-100 hackathon-card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="m-0">{{ $hackathon->title }}</h5>
                                    <span class="badge bg-secondary">Finalizado</span>
                                </div>
                                @if($hackathon->image)
                                    <img src="{{ Storage::url($hackathon->image) }}" class="card-img-top hackathon-image" alt="{{ $hackathon->title }}">
                                @else
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light hackathon-image">
                                        <i class="fas fa-code fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <p class="card-text">{{ Str::limit($hackathon->description, 150) }}</p>
                                    
                                    <div class="hackathon-info mb-3">
                                        <div><i class="fas fa-calendar me-2"></i> <strong>Fecha:</strong> 
                                            {{ \Carbon\Carbon::parse($hackathon->start_date)->format('d M') }} - 
                                            {{ \Carbon\Carbon::parse($hackathon->end_date)->format('d M, Y') }}
                                        </div>
                                        <div><i class="fas fa-users me-2"></i> <strong>Equipos participantes:</strong> 
                                            {{ $hackathon->teams_count }}
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-secondary">{{ $hackathon->status === 'finished' ? 'Finalizado' : 'Pasado' }}</span>
                                            <small class="ms-2 text-muted">{{ \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') }}</small>
                                        </div>
                                        <a href="{{ route('student.hackathons.details', ['id' => $hackathon->id]) }}" class="btn btn-secondary">
                                            <i class="fas fa-eye me-1"></i> Ver detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No hay hackathones finalizados para mostrar.
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .hackathon-card {
        transition: transform 0.3s ease;
    }
    .hackathon-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    .hackathon-image {
        height: 160px;
        object-fit: cover;
    }
    .team-card {
        transition: all 0.3s ease;
    }
    .team-card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endsection 