@extends('layouts.app')

@section('title', 'Gestión de Rondas - ' . $hackathon->title)

@section('header', 'Gestión de Rondas - ' . $hackathon->title)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hackathons.index') }}">Hackathones</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hackathons.show', $hackathon->hackathon_id) }}">{{ $hackathon->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gestión de Rondas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
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

<!-- Información del Hackathon -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Información del Hackathon</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-4 fw-bold">Título:</div>
                    <div class="col-lg-8">{{ $hackathon->title }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-4 fw-bold">Descripción:</div>
                    <div class="col-lg-8">{{ Str::limit($hackathon->description, 150) }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-4 fw-bold">Ronda Actual:</div>
                    <div class="col-lg-8">{{ $hackathon->current_round ?? 'No definida' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-4 fw-bold">Estado:</div>
                    <div class="col-lg-8">
                        @if($hackathon->status == 'active')
                        <span class="badge bg-success">Activo</span>
                        @elseif($hackathon->status == 'finished')
                        <span class="badge bg-secondary">Finalizado</span>
                        @else
                        <span class="badge bg-warning">Pendiente</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 fw-bold">Equipos:</div>
                    <div class="col-lg-8">{{ $hackathon->teams_count ?? $hackathon->teams()->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Gestión de Rondas</h5>
            </div>
            <div class="card-body">
                <p>Las rondas representan las diferentes fases del hackathon. Cada ronda puede tener diferentes objetivos y entregables.</p>
                
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i> Solo puede haber una ronda activa a la vez. Al activar una ronda, las demás se desactivarán automáticamente.
                </div>
                
                <div class="d-grid">
                    <a href="{{ route('hackathons.rounds.create', $hackathon->id) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Crear Nueva Ronda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Timeline de rondas -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Cronograma de Rondas</h5>
    </div>
    <div class="card-body">
        @if($rounds->count() > 0)
            <div class="timeline">
                @foreach($rounds as $round)
                    <div class="timeline-item {{ $round->status == 'active' ? 'active' : '' }}">
                        <div class="timeline-badge {{ $round->status == 'completed' ? 'bg-success' : ($round->status == 'active' ? 'bg-primary' : 'bg-secondary') }}">
                            {{ $round->round_number }}
                        </div>
                        <div class="timeline-content card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $round->name }}</h5>
                                <div>
                                    @if($round->status == 'active')
                                    <span class="badge bg-success">Activa</span>
                                    @elseif($round->status == 'completed')
                                    <span class="badge bg-secondary">Completada</span>
                                    @else
                                    <span class="badge bg-warning">Pendiente</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Inicio:</strong> {{ \Carbon\Carbon::parse($round->start_date)->format('d/m/Y') }}</p>
                                        <p class="mb-1"><strong>Fin:</strong> {{ \Carbon\Carbon::parse($round->end_date)->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            $isActive = $now->between($round->start_date, $round->end_date);
                                            $hasEnded = $now->isAfter($round->end_date);
                                            $hasStarted = $now->isAfter($round->start_date);
                                            $daysRemaining = $now->diffInDays($round->end_date, false);
                                        @endphp
                                        
                                        @if($hasEnded)
                                        <p class="text-muted mb-0">Ronda finalizada hace {{ $now->diffInDays($round->end_date) }} días</p>
                                        @elseif($isActive)
                                        <p class="text-success mb-0">
                                            <i class="fas fa-clock me-1"></i> 
                                            {{ $daysRemaining > 0 ? "Quedan $daysRemaining días" : "Finaliza hoy" }}
                                        </p>
                                        @elseif(!$hasStarted)
                                        <p class="text-muted mb-0">Inicia en {{ $now->diffInDays($round->start_date) }} días</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="card-text">{{ Str::limit($round->description, 150) }}</p>
                                
                                <div class="d-flex justify-content-between">
                                    <div class="btn-group">
                                        <a href="{{ route('hackathons.rounds.edit', [$hackathon->id, $round->id]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </a>
                                        <a href="{{ route('hackathons.deliverables.evaluate.round', [$hackathon->id, $round->id]) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-clipboard-check me-1"></i> Evaluar
                                        </a>
                                    </div>
                                    
                                    <div class="btn-group">
                                        @if($round->status != 'active')
                                        <form action="{{ route('hackathons.rounds.status', [$hackathon->id, $round->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-play me-1"></i> Activar
                                            </button>
                                        </form>
                                        @endif
                                        
                                        @if($round->status != 'completed' && $round->status == 'active')
                                        <form action="{{ route('hackathons.rounds.status', [$hackathon->id, $round->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-check me-1"></i> Completar
                                            </button>
                                        </form>
                                        @endif
                                        
                                        <form action="{{ route('hackathons.rounds.destroy', [$hackathon->id, $round->id]) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash me-1"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @if($round->objectives)
                            <div class="card-footer">
                                <small class="text-muted">
                                    <strong>Objetivos:</strong> {{ Str::limit($round->objectives, 100) }}
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> No hay rondas creadas para este hackathon.
                <a href="{{ route('hackathons.rounds.create', $hackathon->id) }}" class="alert-link">Crear la primera ronda</a>.
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        height: 100%;
        width: 3px;
        background: #e9ecef;
        left: 50px;
        top: 0;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    
    .timeline-badge {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: white;
        text-align: center;
        line-height: 40px;
        font-size: 1.2rem;
        position: absolute;
        left: 30px;
        top: 20px;
        z-index: 1;
    }
    
    .timeline-content {
        margin-left: 80px;
        position: relative;
    }
    
    .timeline-item.active .timeline-content {
        border: 2px solid #0d6efd;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmar eliminación de ronda
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('¿Estás seguro de que deseas eliminar esta ronda? Esta acción no se puede deshacer.')) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection 