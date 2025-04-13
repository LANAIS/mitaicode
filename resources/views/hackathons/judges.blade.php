@extends('layouts.app')

@section('title', 'Gestión de Jurados - ' . $hackathon->title)

@section('header', 'Gestión de Jurados')

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
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.show', $hackathon->id) }}">{{ $hackathon->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gestión de Jurados</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('hackathons.show', $hackathon->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al hackathon
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Jurados - {{ $hackathon->title }}</h1>
        <a href="{{ route('hackathons.show', $hackathon->id) }}" class="btn btn-outline-secondary btn-sm">
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
                            <strong>{{ $hackathon->start_date ? \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') : 'TBD' }}</strong>
                        </div>
                        <div>
                            <span class="text-muted">Fin:</span>
                            <strong>{{ $hackathon->end_date ? \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') : 'TBD' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Añadir Jurado</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hackathons.judges.update', $hackathon->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Seleccionar Usuario</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Seleccionar...</option>
                                @foreach($potentialJudges as $user)
                                    <option value="{{ $user->user_id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_lead_judge" name="is_lead_judge">
                                <label class="form-check-label" for="is_lead_judge">
                                    Jurado principal
                                </label>
                                <div class="form-text">Los jurados principales tienen capacidad de decisión final sobre los equipos ganadores.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas (opcional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            <div class="form-text">Información adicional sobre el jurado (especialidad, responsabilidades, etc.)</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-plus-circle"></i> Añadir Jurado
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Jurados Asignados</h5>
                    <span class="badge bg-info">{{ count($hackathon->judges ?? []) }}</span>
                </div>
                
                <div class="card-body">
                    @if(isset($hackathon->judges) && $hackathon->judges->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Jurado</th>
                                        <th scope="col">Correo</th>
                                        <th scope="col">Rol</th>
                                        <th scope="col">Notas</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hackathon->judges as $judge)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; overflow: hidden;">
                                                        <img src="https://api.dicebear.com/7.x/adventurer/svg?seed={{ $judge->user_id }}" class="rounded-circle" width="36" height="36" alt="Avatar">
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $judge->first_name }} {{ $judge->last_name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $judge->email }}</td>
                                            <td>
                                                @if($judge->pivot->is_lead_judge)
                                                    <span class="badge bg-primary">Jurado principal</span>
                                                @else
                                                    <span class="badge bg-secondary">Jurado</span>
                                                @endif
                                            </td>
                                            <td>{{ $judge->pivot->notes ?? '-' }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editJudgeModal{{ $judge->user_id }}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#removeJudgeModal{{ $judge->user_id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Modal para editar jurado -->
                                                <div class="modal fade" id="editJudgeModal{{ $judge->user_id }}" tabindex="-1" aria-labelledby="editJudgeModalLabel{{ $judge->user_id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editJudgeModalLabel{{ $judge->user_id }}">Editar Jurado</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="{{ route('hackathons.judges.update', [$hackathon->id, $judge->user_id]) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" value="1" id="is_lead_judge_{{ $judge->user_id }}" name="is_lead_judge" {{ $judge->pivot->is_lead_judge ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="is_lead_judge_{{ $judge->user_id }}">
                                                                                Jurado principal
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="notes_{{ $judge->user_id }}" class="form-label">Notas</label>
                                                                        <textarea class="form-control" id="notes_{{ $judge->user_id }}" name="notes" rows="3">{{ $judge->pivot->notes }}</textarea>
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
                                                
                                                <!-- Modal para eliminar jurado -->
                                                <div class="modal fade" id="removeJudgeModal{{ $judge->user_id }}" tabindex="-1" aria-labelledby="removeJudgeModalLabel{{ $judge->user_id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="removeJudgeModalLabel{{ $judge->user_id }}">Confirmar eliminación</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>¿Estás seguro de que deseas eliminar a <strong>{{ $judge->first_name }} {{ $judge->last_name }}</strong> como jurado de este hackathon?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <form action="{{ route('hackathons.judges.destroy', [$hackathon->id, $judge->user_id]) }}" method="POST">
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
                            <i class="fas fa-info-circle me-2"></i> No hay jurados asignados a este hackathon.
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
@endsection 