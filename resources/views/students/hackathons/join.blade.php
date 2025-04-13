@extends('layouts.app')

@section('title', 'Participar en ' . $hackathon->title)

@section('header', 'Participar en el Hackathon')

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
                    <li class="breadcrumb-item"><a href="{{ route('student.hackathons.details', ['id' => $hackathon->id]) }}">{{ $hackathon->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Participar</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('student.hackathons.details', ['id' => $hackathon->id]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver a los detalles
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Participar en {{ $hackathon->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Únete al hackathon</h5>
                                <p class="mb-0">Puedes unirte a un equipo existente o crear tu propio equipo para participar en este hackathon.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs para seleccionar opción -->
                    <ul class="nav nav-tabs mt-4" id="joinTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" 
                                    type="button" role="tab" aria-controls="create" aria-selected="true">
                                <i class="fas fa-plus-circle me-1"></i> Crear equipo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="join-tab" data-bs-toggle="tab" data-bs-target="#join" 
                                    type="button" role="tab" aria-controls="join" aria-selected="false">
                                <i class="fas fa-sign-in-alt me-1"></i> Unirse a equipo 
                                <span class="badge bg-info ms-1">{{ count($availableTeams) }}</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Contenido de las tabs -->
                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="joinTabContent">
                        <!-- Crear equipo -->
                        <div class="tab-pane fade show active" id="create" role="tabpanel" aria-labelledby="create-tab">
                            <h5 class="mb-3">Crear un nuevo equipo</h5>
                            <form action="{{ route('student.hackathons.create-team', ['id' => $hackathon->id]) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="team_name" class="form-label">Nombre del equipo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('team_name') is-invalid @enderror" id="team_name" name="team_name" value="{{ old('team_name') }}" required>
                                    @error('team_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Elige un nombre creativo y representativo para tu equipo</div>
                                </div>

                                <div class="mb-3">
                                    <label for="team_description" class="form-label">Descripción <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('team_description') is-invalid @enderror" id="team_description" name="team_description" rows="3" required>{{ old('team_description') }}</textarea>
                                    @error('team_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Describe brevemente el enfoque de tu equipo para este hackathon</div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle me-2"></i> Al crear un nuevo equipo, automáticamente serás asignado como líder del equipo.
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Crear equipo
                                </button>
                            </form>
                        </div>

                        <!-- Unirse a equipo -->
                        <div class="tab-pane fade" id="join" role="tabpanel" aria-labelledby="join-tab">
                            <h5 class="mb-3">Unirse a un equipo existente</h5>
                            
                            @if($availableTeams->count() > 0)
                                <div class="list-group">
                                    @foreach($availableTeams as $availableTeam)
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-1">{{ $availableTeam->name }}</h5>
                                                    <p class="mb-1">{{ Str::limit($availableTeam->description, 100) }}</p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-users me-1"></i> 
                                                        {{ $availableTeam->members->count() }} de {{ $hackathon->team_size }} miembros
                                                    </small>
                                                </div>
                                                <form action="{{ route('student.hackathons.join-team', ['id' => $hackathon->id]) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="team_id" value="{{ $availableTeam->id }}">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-sign-in-alt me-1"></i> Unirse
                                                    </button>
                                                </form>
                                            </div>
                                            @if($availableTeam->members->count() > 0)
                                                <div class="mt-2">
                                                    <small class="text-muted">Miembros:</small>
                                                    <div class="d-flex mt-1">
                                                        @foreach($availableTeam->members as $member)
                                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-1" 
                                                                style="width: 30px; height: 30px; font-size: 12px;" 
                                                                title="{{ $member->first_name }} {{ $member->last_name }}">
                                                                {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                                                @if($availableTeam->isLeader($member->user_id))
                                                                    <i class="fas fa-crown text-warning" style="font-size: 8px; position: absolute; top: -2px; right: -2px;"></i>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> No hay equipos disponibles para unirse en este momento.
                                    <p class="mb-0 mt-2">Puedes crear tu propio equipo y otros estudiantes podrán unirse a él.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('student.hackathons.details', ['id' => $hackathon->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Información del Hackathon</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ $hackathon->title }}</h6>
                            <p>{{ Str::limit($hackathon->description, 150) }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="hackathon-info">
                                <div class="mb-1">
                                    <i class="fas fa-calendar me-2 text-primary"></i> 
                                    <strong>Fecha:</strong> 
                                    {{ \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') }}
                                </div>
                                <div class="mb-1">
                                    <i class="fas fa-users me-2 text-primary"></i> 
                                    <strong>Equipos:</strong> 
                                    {{ $hackathon->teams()->count() }} de {{ $hackathon->max_teams }}
                                </div>
                                <div class="mb-1">
                                    <i class="fas fa-user-friends me-2 text-primary"></i> 
                                    <strong>Tamaño de equipo:</strong> 
                                    Máximo {{ $hackathon->team_size }} miembros
                                </div>
                                <div>
                                    <i class="fas fa-layer-group me-2 text-primary"></i> 
                                    <strong>Rondas:</strong> 
                                    {{ $hackathon->rounds()->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 