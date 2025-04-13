@extends('layouts.app')

@section('title', ($isEdit ? 'Editar' : 'Crear') . ' Ronda - ' . $hackathon->title)

@section('header', ($isEdit ? 'Editar' : 'Crear') . ' Ronda - ' . $hackathon->title)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hackathons.index') }}">Hackathones</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hackathons.show', $hackathon->hackathon_id) }}">{{ $hackathon->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hackathons.rounds.index', $hackathon->hackathon_id) }}">Gestión de Rondas</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? 'Editar' : 'Crear' }} Ronda</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <!-- Formulario de ronda -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ $isEdit ? 'Editar' : 'Nueva' }} Ronda</h5>
            </div>
            <div class="card-body">
                <form action="{{ $isEdit ? route('hackathons.rounds.update', [$hackathon->id, $round->id]) : route('hackathons.rounds.store', $hackathon->id) }}" method="POST">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nombre de la Ronda <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $round->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Ejemplo: "Ideación", "Desarrollo", "Presentación Final"</div>
                        </div>
                        <div class="col-md-4">
                            <label for="round_number" class="form-label">Número de Ronda <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('round_number') is-invalid @enderror" id="round_number" name="round_number" value="{{ old('round_number', $isEdit ? $round->round_number : $roundNumber) }}" min="1" required>
                            @error('round_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Orden de esta ronda en el hackathon</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $round->start_date ? \Carbon\Carbon::parse($round->start_date)->format('Y-m-d') : '') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Fecha de Finalización <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $round->end_date ? \Carbon\Carbon::parse($round->end_date)->format('Y-m-d') : '') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $round->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Breve descripción de lo que se espera en esta ronda</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="objectives" class="form-label">Objetivos</label>
                        <textarea class="form-control @error('objectives') is-invalid @enderror" id="objectives" name="objectives" rows="4">{{ old('objectives', $round->objectives) }}</textarea>
                        @error('objectives')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Metas específicas que deben alcanzar los equipos en esta ronda</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="requirements" class="form-label">Requisitos</label>
                        <textarea class="form-control @error('requirements') is-invalid @enderror" id="requirements" name="requirements" rows="4">{{ old('requirements', $round->requirements) }}</textarea>
                        @error('requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Requisitos técnicos o criterios de evaluación para los entregables</div>
                    </div>
                    
                    @if($isEdit)
                    <div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="pending" {{ old('status', $round->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="active" {{ old('status', $round->status) == 'active' ? 'selected' : '' }}>Activa</option>
                            <option value="completed" {{ old('status', $round->status) == 'completed' ? 'selected' : '' }}>Completada</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Estado actual de la ronda</div>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('hackathons.rounds.index', $hackathon->hackathon_id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> {{ $isEdit ? 'Actualizar' : 'Crear' }} Ronda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Información adicional -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Información del Hackathon</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Título:</strong> {{ $hackathon->title }}</p>
                <p class="mb-2"><strong>Ronda actual:</strong> {{ $hackathon->current_round ?? 'No definida' }}</p>
                <p class="mb-0"><strong>Fecha de inicio:</strong> {{ $hackathon->start_date ? \Carbon\Carbon::parse($hackathon->start_date)->format('d/m/Y') : 'No definida' }}</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Ayuda</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-bold">Número de Ronda</h6>
                    <p class="text-muted">Determina el orden de las rondas en el hackathon. Solo puede haber una ronda con cada número.</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">Fechas</h6>
                    <p class="text-muted">Las fechas determinan cuándo estará disponible la ronda para los participantes. La fecha de finalización debe ser posterior a la de inicio.</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">Estados</h6>
                    <ul class="text-muted mb-0">
                        <li><strong>Pendiente:</strong> La ronda aún no ha comenzado.</li>
                        <li><strong>Activa:</strong> Es la ronda actual del hackathon.</li>
                        <li><strong>Completada:</strong> La ronda ha finalizado.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validar que la fecha de fin sea posterior a la de inicio
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        function validateDates() {
            if (startDateInput.value && endDateInput.value) {
                if (new Date(endDateInput.value) <= new Date(startDateInput.value)) {
                    endDateInput.setCustomValidity('La fecha de finalización debe ser posterior a la fecha de inicio');
                } else {
                    endDateInput.setCustomValidity('');
                }
            }
        }
        
        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);
    });
</script>
@endsection 