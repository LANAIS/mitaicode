@extends('layouts.app')

@section('title', 'Mis Hackathones - Evaluación')

@section('header', 'Mis Hackathones - Evaluación')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3">Hackathones a Evaluar</h1>
            <p class="text-muted">Gestiona y evalúa los hackathones donde participas como creador o juez</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('hackathons.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Crear Nuevo Hackathon
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('teacher.hackathons') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Estado</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Próximos</option>
                        <option value="finished" {{ request('status') == 'finished' ? 'selected' : '' }}>Finalizados</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="role" class="form-label">Mi Rol</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">Todos</option>
                        <option value="creator" {{ request('role') == 'creator' ? 'selected' : '' }}>Creador</option>
                        <option value="judge" {{ request('role') == 'judge' ? 'selected' : '' }}>Juez</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="pending" class="form-label">Entregables</label>
                    <select name="pending" id="pending" class="form-select">
                        <option value="">Todos</option>
                        <option value="pending" {{ request('pending') == 'pending' ? 'selected' : '' }}>Con entregables pendientes</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('teacher.hackathons') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Hackathones -->
    @if($hackathons->count() > 0)
        <div class="row">
            @foreach($hackathons as $hackathon)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-{{ $hackathon->pending_deliverables_count > 0 ? 'danger' : ($hackathon->status == 'active' ? 'success' : ($hackathon->status == 'pending' ? 'warning' : 'secondary')) }}">
                        <div class="card-header d-flex justify-content-between align-items-center 
                               bg-{{ $hackathon->pending_deliverables_count > 0 ? 'danger text-white' : ($hackathon->status == 'active' ? 'success text-white' : ($hackathon->status == 'pending' ? 'warning' : 'secondary text-white')) }}">
                            <h5 class="mb-0">{{ $hackathon->title }}</h5>
                            <div>
                                @if($hackathon->pending_deliverables_count > 0)
                                    <span class="badge bg-light text-danger">{{ $hackathon->pending_deliverables_count }} entregables pendientes</span>
                                @endif
                                <span class="badge bg-light text-{{ $hackathon->status == 'active' ? 'success' : ($hackathon->status == 'pending' ? 'warning' : 'secondary') }}">
                                    {{ $hackathon->status == 'active' ? 'Activo' : ($hackathon->status == 'pending' ? 'Próximamente' : 'Finalizado') }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold">Fechas:</div>
                                <div class="col-lg-8">
                                    {{ \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($hackathon->end_date)->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold">Equipos:</div>
                                <div class="col-lg-8">{{ $hackathon->teams_count }} de {{ $hackathon->max_teams }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold">Rondas:</div>
                                <div class="col-lg-8">{{ $hackathon->rounds_count }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold">Ronda Actual:</div>
                                <div class="col-lg-8">
                                    @if($hackathon->active_round)
                                        {{ $hackathon->active_round->title ?? 'Ronda ' . $hackathon->active_round->round_number }}
                                    @else
                                        <span class="badge bg-warning">No iniciado</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 fw-bold">Mi Rol:</div>
                                <div class="col-lg-8">
                                    @if($hackathon->created_by == Auth::id())
                                        <span class="badge bg-primary">Creador</span>
                                    @endif
                                    @if($hackathon->isJudge(Auth::id()))
                                        <span class="badge bg-info">Juez</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-between">
                            <div>
                                <a href="{{ route('hackathons.show', $hackathon->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye me-1"></i> Ver Detalles
                                </a>
                                @if($hackathon->created_by == Auth::id())
                                <a href="{{ route('hackathons.edit', $hackathon->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Editar
                                </a>
                                @endif
                            </div>
                            @if($hackathon->pending_deliverables_count > 0)
                                <a href="{{ route('hackathons.deliverables.evaluate', $hackathon->id) }}" class="btn btn-sm btn-danger">
                                    <i class="fas fa-clipboard-check me-1"></i> Evaluar Entregables
                                </a>
                            @elseif($hackathon->status == 'active')
                                <a href="{{ route('hackathons.deliverables.evaluate', $hackathon->id) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-clipboard-check me-1"></i> Ver Evaluaciones
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> No hay hackathones disponibles para evaluar. 
            @if(Auth::user()->role == 'teacher')
                <a href="{{ route('hackathons.create') }}" class="alert-link">Crea un nuevo hackathon</a> para comenzar.
            @endif
        </div>
    @endif
</div>

<style>
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection 