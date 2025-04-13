@extends('layouts.app')

@section('title', 'Gestión de Rondas - ' . $hackathon->title)

@section('header', 'Gestión de Rondas')

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
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.show', ['id' => $hackathon->id]) }}">{{ $hackathon->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gestión de Rondas</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('hackathons.show', ['id' => $hackathon->id]) }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Volver al hackathon
            </a>
            <a href="{{ route('teacher.hackathon.edit', ['hackathon_id' => $hackathon->id]) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-edit"></i> Editar hackathon
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Rondas - {{ $hackathon->title }}</h1>
        <a href="{{ route('hackathons.show', ['id' => $hackathon->id]) }}" class="btn btn-outline-secondary btn-sm">
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
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted">Inicio:</span>
                            <strong>{{ $hackathon->start_date ? \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') : 'Por definir' }}</strong>
                        </div>
                        <div>
                            <span class="text-muted">Fin:</span>
                            <strong>{{ $hackathon->end_date ? \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') : 'Por definir' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Añadir Nueva Ronda</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hackathons.rounds.store', ['hackathonId' => $hackathon->id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre de la Ronda</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="objectives" class="form-label">Objetivos</label>
                            <textarea class="form-control @error('objectives') is-invalid @enderror" id="objectives" name="objectives" rows="3">{{ old('objectives') }}</textarea>
                            @error('objectives')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Define los objetivos específicos que los equipos deben alcanzar en esta ronda.</div>
                        </div>

                        <div class="mb-3">
                            <label for="deliverables" class="form-label">Entregables</label>
                            <textarea class="form-control @error('deliverables') is-invalid @enderror" id="deliverables" name="deliverables" rows="3">{{ old('deliverables') }}</textarea>
                            @error('deliverables')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Especifica los documentos, código, presentaciones u otros materiales que los equipos deben entregar.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Ronda activa actualmente
                            </label>
                            <div class="form-text">Solo una ronda puede estar activa a la vez. Activar esta ronda desactivará cualquier otra ronda activa.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus-circle"></i> Añadir Ronda
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rondas Configuradas</h5>
                    <span class="badge bg-info">{{ count($hackathon->rounds ?? []) }}</span>
                </div>
                
                <div class="card-body">
                    @if(isset($hackathon->rounds) && $hackathon->rounds->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 40px">#</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Fechas</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="sortable-rounds">
                                    @foreach($hackathon->rounds->sortBy('start_date') as $index => $round)
                                        <tr data-id="{{ $round->round_id }}" class="{{ $round->is_active ? 'table-success' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <h6 class="mb-0">{{ $round->name }}</h6>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-alt me-1"></i> 
                                                        {{ $round->start_date->format('d/m/Y') }} - {{ $round->end_date->format('d/m/Y') }}
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i> 
                                                        {{ $round->start_date->diffInDays($round->end_date) }} días
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($round->is_active)
                                                    <span class="badge bg-success">Activa</span>
                                                @else
                                                    @if($round->start_date->isPast() && $round->end_date->isFuture())
                                                        <span class="badge bg-warning text-dark">En curso</span>
                                                    @elseif($round->start_date->isFuture())
                                                        <span class="badge bg-secondary">Pendiente</span>
                                                    @else
                                                        <span class="badge bg-secondary">Finalizada</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoundModal{{ $round->round_id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @if(!$round->is_active)
                                                        <form action="{{ route('hackathons.rounds.status', ['hackathonId' => $hackathon->id, 'roundId' => $round->round_id]) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="active" value="true">
                                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoundModal{{ $round->round_id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Modal para editar ronda -->
                                                <div class="modal fade" id="editRoundModal{{ $round->round_id }}" tabindex="-1" aria-labelledby="editRoundModalLabel{{ $round->round_id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editRoundModalLabel{{ $round->round_id }}">Editar Ronda: {{ $round->name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('hackathons.rounds.update', ['hackathonId' => $hackathon->id, 'roundId' => $round->round_id]) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="mb-3">
                                                                                <label for="name_{{ $round->round_id }}" class="form-label">Nombre de la Ronda</label>
                                                                                <input type="text" class="form-control" id="name_{{ $round->round_id }}" name="name" value="{{ $round->name }}" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="mb-3">
                                                                                <label for="start_date_{{ $round->round_id }}" class="form-label">Fecha de Inicio</label>
                                                                                <input type="date" class="form-control" id="start_date_{{ $round->round_id }}" name="start_date" value="{{ $round->start_date->format('Y-m-d') }}" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="mb-3">
                                                                                <label for="end_date_{{ $round->round_id }}" class="form-label">Fecha de Fin</label>
                                                                                <input type="date" class="form-control" id="end_date_{{ $round->round_id }}" name="end_date" value="{{ $round->end_date->format('Y-m-d') }}" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="objectives_{{ $round->round_id }}" class="form-label">Objetivos</label>
                                                                        <textarea class="form-control" id="objectives_{{ $round->round_id }}" name="objectives" rows="3">{{ $round->objectives }}</textarea>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="deliverables_{{ $round->round_id }}" class="form-label">Entregables</label>
                                                                        <textarea class="form-control" id="deliverables_{{ $round->round_id }}" name="deliverables" rows="3">{{ $round->deliverables }}</textarea>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="description_{{ $round->round_id }}" class="form-label">Descripción</label>
                                                                        <textarea class="form-control" id="description_{{ $round->round_id }}" name="description" rows="2">{{ $round->description }}</textarea>
                                                                    </div>
                                                                    
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" value="1" id="is_active_{{ $round->round_id }}" name="is_active" {{ $round->is_active ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="is_active_{{ $round->round_id }}">
                                                                            Ronda activa actualmente
                                                                        </label>
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
                                                
                                                <!-- Modal para eliminar ronda -->
                                                <div class="modal fade" id="deleteRoundModal{{ $round->round_id }}" tabindex="-1" aria-labelledby="deleteRoundModalLabel{{ $round->round_id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteRoundModalLabel{{ $round->round_id }}">Eliminar Ronda</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Está seguro que desea eliminar la ronda <strong>{{ $round->name }}</strong>?</p>
                                                                <p class="text-danger">Esta acción no se puede deshacer. Se eliminarán también todos los entregables asociados a esta ronda.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <form action="{{ route('hackathons.rounds.destroy', ['hackathonId' => $hackathon->id, 'roundId' => $round->round_id]) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Eliminar Ronda</button>
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
                            <i class="fas fa-info-circle me-2"></i> No hay rondas configuradas para este hackathon.
                        </div>
                    @endif
                </div>
                
                @if(isset($hackathon->rounds) && $hackathon->rounds->count() > 0)
                    <div class="card-footer">
                        <h6>Consejos para la gestión de rondas:</h6>
                        <ul class="small text-muted">
                            <li>Incluya objetivos claros y específicos para cada ronda</li>
                            <li>Especifique con detalle los entregables esperados para cada fase</li>
                            <li>Considere las fechas para que los equipos tengan tiempo suficiente</li>
                            <li>Solo una ronda puede estar activa a la vez</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .table td {
        vertical-align: middle;
    }
    
    .sortable-rounds tr {
        cursor: pointer;
    }
    
    .sortable-rounds tr:hover {
        background-color: rgba(0,0,0,0.03);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validación de fechas
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        
        if (startDate && endDate) {
            startDate.addEventListener('change', function() {
                endDate.min = startDate.value;
                if (endDate.value && new Date(endDate.value) < new Date(startDate.value)) {
                    endDate.value = startDate.value;
                }
            });
            
            // Establecer fechas mínimas
            const today = new Date().toISOString().split('T')[0];
            startDate.min = today;
            if (!startDate.value) {
                endDate.min = today;
            }
        }
    });
</script>
@endsection 