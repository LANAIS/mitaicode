@extends('layouts.app')

@section('title', 'Hackathones')

@section('header', 'Hackathones')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x me-3"></i>
                <div>
                    <h5 class="mb-0">¡Operación exitosa!</h5>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                <div>
                    <h5 class="mb-0">Error</h5>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Hackathones disponibles</h1>
        </div>
        <div class="col-md-4 text-md-end">
            @if(Auth::user()->role === 'teacher' || Auth::user()->role === 'admin')
                <a href="{{ route('teacher.hackathon.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Hackathon
                </a>
            @endif
        </div>
    </div>

    <div class="mb-4">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ !request('filter') ? 'active' : '' }}" href="{{ route('hackathons.index') }}">
                    Todos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'active' ? 'active' : '' }}" 
                   href="{{ route('hackathons.index', ['filter' => 'active']) }}">
                    Activos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'pending' ? 'active' : '' }}" 
                   href="{{ route('hackathons.index', ['filter' => 'pending']) }}">
                    Próximos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'finished' ? 'active' : '' }}" 
                   href="{{ route('hackathons.index', ['filter' => 'finished']) }}">
                    Finalizados
                </a>
            </li>
        </ul>
    </div>

    @if($hackathons->count() > 0)
        @php
            // Verificar en qué hackathones está inscrito el estudiante actual
            $participatingHackathonIds = [];
            if (Auth::user()->role === 'student') {
                try {
                    $participatingHackathonIds = DB::table('hackathon_team_user')
                        ->join('hackathon_teams', 'hackathon_team_user.team_id', '=', 'hackathon_teams.team_id')
                        ->where('hackathon_team_user.user_id', Auth::id())
                        ->pluck('hackathon_teams.hackathon_id')
                        ->toArray();
                } catch(\Exception $e) {
                    // Si hay error, mantenemos el array vacío
                }
            }
        @endphp
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($hackathons as $hackathon)
                <div class="col">
                    <div class="card h-100 hackathon-card">
                        @if(Auth::id() === $hackathon->created_by || Auth::user()->role === 'admin')
                            @if(isset($hackathon->id))
                                <a href="{{ route('teacher.hackathon.edit', ['hackathon_id' => $hackathon->id]) }}" class="card-link-overlay"></a>
                            @endif
                        @else
                            @if(isset($hackathon->id))
                                <a href="{{ route('hackathons.show', ['id' => $hackathon->id]) }}" class="card-link-overlay"></a>
                            @endif
                        @endif
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $hackathon->title }}</h5>
                            <span class="badge bg-{{ $hackathon->status === 'active' ? 'success' : ($hackathon->status === 'pending' ? 'warning' : 'secondary') }}">
                                {{ $hackathon->status === 'active' ? 'Activo' : ($hackathon->status === 'pending' ? 'Próximo' : 'Finalizado') }}
                            </span>
                        </div>
                        @if($hackathon->image)
                            <img src="{{ Storage::url($hackathon->image) }}" class="card-img-top hackathon-image" alt="{{ $hackathon->title }}">
                        @else
                            <div class="card-img-top hackathon-image-placeholder d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-laptop-code fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <p class="card-text">{{ Str::limit($hackathon->description, 100) }}</p>
                            
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">Equipos:</small>
                                    <small>{{ $hackathon->teams_count ?? '0' }} / {{ $hackathon->max_teams }}</small>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">Participantes:</small>
                                    <small>{{ $hackathon->participants_count ?? '0' }} / {{ $hackathon->max_participants }}</small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Tamaño de equipos:</small>
                                    <small>{{ $hackathon->team_size }} máx.</small>
                                </div>
                            </div>
                            
                            @if(Auth::user()->role === 'student' && $hackathon->status === 'active' && isset($hackathon->id))
                            <div class="mt-3">
                                @php
                                    $isParticipating = in_array($hackathon->id, $participatingHackathonIds ?? []);
                                    $teamId = null;
                                    
                                    if ($isParticipating) {
                                        try {
                                            $teamData = DB::table('hackathon_team_user')
                                                ->join('hackathon_teams', 'hackathon_team_user.team_id', '=', 'hackathon_teams.team_id')
                                                ->where('hackathon_team_user.user_id', Auth::id())
                                                ->where('hackathon_teams.hackathon_id', $hackathon->id)
                                                ->first();
                                            
                                            if ($teamData) {
                                                $teamId = $teamData->team_id;
                                            }
                                        } catch(\Exception $e) {
                                            // Si hay error, mantener teamId como null
                                        }
                                    }
                                @endphp
                                
                                @if($isParticipating)
                                    <div class="alert alert-success py-2 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <span>Ya estás participando en este hackathon</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.hackathons.team', $teamId ?? $hackathon->id) }}" class="btn btn-primary w-100">
                                        <i class="fas fa-users me-2"></i> Ver mi equipo
                                    </a>
                                @else
                                    <div class="alert alert-info py-2 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <span>¡Inscríbete y participa en este hackathon!</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.hackathons.join', $hackathon->id) }}" class="btn btn-success w-100">
                                        <i class="fas fa-sign-in-alt me-2"></i> Inscribirse ahora
                                    </a>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    {{ $hackathon->start_date ? \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') : 'Fecha por definir' }}
                                    -
                                    {{ $hackathon->end_date ? \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') : '' }}
                                </small>
                                <div>
                                    @if(Auth::id() === $hackathon->created_by || Auth::user()->role === 'admin')
                                        @if(isset($hackathon->id))
                                            <a href="{{ route('teacher.hackathon.edit', ['hackathon_id' => $hackathon->id]) }}" class="btn btn-sm btn-outline-warning me-1">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        @endif
                                    @endif
                                    @if(isset($hackathon->id))
                                        <a href="{{ route('hackathons.show', ['id' => $hackathon->id]) }}" class="btn btn-sm btn-outline-primary">Ver detalles</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $hackathons->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <p class="mb-0">No hay hackathones disponibles en este momento.</p>
            @if(Auth::user()->role === 'teacher' || Auth::user()->role === 'admin')
                <p class="mb-0 mt-2">
                    <a href="{{ route('teacher.hackathon.create') }}" class="alert-link">Crear un nuevo hackathon</a>
                </p>
            @endif
        </div>
    @endif
</div>

<style>
    .hackathon-card {
        transition: transform 0.3s ease;
        position: relative;
    }
    .hackathon-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .hackathon-image, .hackathon-image-placeholder {
        height: 140px;
        object-fit: cover;
    }
    .card-link-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
    }
    .card-footer a, .card-body a, .hackathon-card .btn {
        position: relative;
        z-index: 20;
    }
</style>
@endsection 