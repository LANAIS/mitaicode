@extends('layouts.app')

@section('title', 'Equipos - ' . $hackathon->title)

@section('header', 'Gestión de Equipos')

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
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.show', $hackathon->hackathon_id) }}">{{ $hackathon->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Equipos</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('hackathons.show', $hackathon->hackathon_id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al hackathon
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Equipos - {{ $hackathon->title }}</h1>
        <a href="{{ route('hackathons.show', $hackathon->hackathon_id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver al hackathon
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Hackathon</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $hackathon->title }}</h6>
                    <p class="text-muted small">{{ Str::limit($hackathon->description, 150) }}</p>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <span class="text-muted">Inicio:</span>
                            <strong>{{ $hackathon->start_date ? \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') : 'Por definir' }}</strong>
                        </div>
                        <div>
                            <span class="text-muted">Fin:</span>
                            <strong>{{ $hackathon->end_date ? \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') : 'Por definir' }}</strong>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-2 mb-3">
                                <h6 class="mb-1">Equipos</h6>
                                <p class="mb-0 fs-4">{{ $hackathon->teams_count ?? count($teams ?? []) }} / {{ $hackathon->max_teams }}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 mb-3">
                                <h6 class="mb-1">Participantes</h6>
                                <p class="mb-0 fs-4">{{ $hackathon->participants_count ?? $totalParticipants }} / {{ $hackathon->max_participants }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> El tamaño máximo por equipo es de <strong>{{ $hackathon->team_size }}</strong> participantes.
                    </div>
                </div>
            </div>

            @if($hackathon->status === 'active' && (Auth::id() === $hackathon->created_by || Auth::user()->role === 'admin'))
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Opciones de Inscripción</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('hackathons.teams.settings', $hackathon->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_registrations" name="allow_registrations" {{ $hackathon->allow_registrations ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_registrations">Permitir inscripciones</label>
                                </div>
                                <div class="form-text">Activar/desactivar inscripciones para nuevos equipos.</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_team_changes" name="allow_team_changes" {{ $hackathon->allow_team_changes ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_team_changes">Permitir cambios de equipo</label>
                                </div>
                                <div class="form-text">Permite a los participantes cambiar de equipo o abandonar el hackathon.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> Guardar configuración
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Equipos Inscritos</h5>
                    <span class="badge bg-info">{{ count($teams ?? []) }}</span>
                </div>
                
                <div class="card-body">
                    @if(isset($teams) && count($teams) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Equipo</th>
                                        <th scope="col">Miembros</th>
                                        <th scope="col">Creado</th>
                                        <th scope="col" class="text-center">Entregas</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teams as $team)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="team-avatar me-2 d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 40px; height: 40px;">
                                                        <span class="fw-bold text-secondary">{{ substr($team->name, 0, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $team->name }}</h6>
                                                        <span class="text-muted small">{{ $team->members_count ?? count($team->members) }} miembros</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($team->members->take(3) as $member)
                                                        <img src="{{ $member->profile_photo_url ?? 'https://via.placeholder.com/30' }}" class="rounded-circle" width="30" height="30" alt="{{ $member->name }}">
                                                    @endforeach
                                                    @if(($team->members_count ?? count($team->members)) > 3)
                                                        <div class="avatar-group-more d-flex align-items-center justify-content-center bg-secondary rounded-circle text-white" style="width: 30px; height: 30px;">
                                                            <small>+{{ ($team->members_count ?? count($team->members)) - 3 }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $team->created_at->format('d/m/Y') }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $team->deliverables_count > 0 ? 'success' : 'secondary' }}">
                                                    {{ $team->deliverables_count ?? 0 }} / {{ $hackathon->rounds_count }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('hackathons.team', $team->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#removeTeamModal{{ $team->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Modal para eliminar equipo -->
                                                <div class="modal fade" id="removeTeamModal{{ $team->id }}" tabindex="-1" aria-labelledby="removeTeamModalLabel{{ $team->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="removeTeamModalLabel{{ $team->id }}">Confirmar eliminación</h5>
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
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Aún no hay equipos inscritos en este hackathon.
                        </div>
                    @endif
                </div>
                
                @if(isset($teams) && count($teams) > 0)
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-export"></i> Exportar equipos
                                </a>
                            </div>
                            <div class="small text-muted">
                                Última actualización: {{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Listado de participantes -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Participantes</h5>
                    <div>
                        <input type="text" id="participantSearch" class="form-control form-control-sm" placeholder="Buscar participante...">
                    </div>
                </div>
                
                <div class="card-body">
                    @if(isset($participants) && count($participants) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" id="participantsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Participante</th>
                                        <th scope="col">Equipo</th>
                                        <th scope="col">Rol</th>
                                        <th scope="col">Ingresó</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($participants as $participant)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $participant->profile_photo_url ?? 'https://via.placeholder.com/36' }}" class="rounded-circle me-2" width="36" alt="Avatar">
                                                    <div>
                                                        <h6 class="mb-0">{{ $participant->name }}</h6>
                                                        <small class="text-muted">{{ $participant->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($participant->team)
                                                    <a href="{{ route('hackathons.team', $participant->team->id) }}" class="text-decoration-none">
                                                        {{ $participant->team->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Sin equipo</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($participant->pivot && $participant->pivot->is_leader)
                                                    <span class="badge bg-primary">Líder</span>
                                                @else
                                                    <span class="badge bg-secondary">Miembro</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $participant->pivot ? $participant->pivot->created_at->format('d/m/Y') : '-' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Aún no hay participantes inscritos en este hackathon.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table td {
        vertical-align: middle;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtrado de participantes
        const searchInput = document.getElementById('participantSearch');
        const table = document.getElementById('participantsTable');
        
        if (searchInput && table) {
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchText) ? '' : 'none';
                });
            });
        }
    });
</script>
@endsection 