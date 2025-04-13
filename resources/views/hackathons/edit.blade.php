@extends('layouts.app')

@section('title', 'Editar Hackathon')

@section('header', 'Editar Hackathon')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.index') }}">Hackathones</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.show', ['id' => $hackathon->id]) }}">{{ $hackathon->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Editar información del Hackathon</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.hackathon.update', $hackathon->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $hackathon->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $hackathon->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ (old('status', $hackathon->status) === 'pending') ? 'selected' : '' }}>Pendiente (próximamente)</option>
                                <option value="active" {{ (old('status', $hackathon->status) === 'active') ? 'selected' : '' }}>Activo</option>
                                <option value="finished" {{ (old('status', $hackathon->status) === 'finished') ? 'selected' : '' }}>Finalizado</option>
                            </select>
                            <div class="form-text mt-2">
                                <div class="alert alert-info mb-0 small">
                                    <ul class="mb-0 ps-3">
                                        <li><strong>Pendiente:</strong> El hackathon está visible pero no se permiten inscripciones.</li>
                                        <li><strong>Activo:</strong> El hackathon permite inscripciones y participación.</li>
                                        <li><strong>Finalizado:</strong> El hackathon ha concluido, no permite más actividad.</li>
                                    </ul>
                                </div>
                            </div>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagen del Hackathon</label>
                            @if($hackathon->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($hackathon->image) }}" alt="{{ $hackathon->title }}" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                            <div class="form-text">Formato recomendado: JPG/PNG, máximo 2MB. Deja en blanco para mantener la imagen actual.</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $hackathon->start_date ? \Carbon\Carbon::parse($hackathon->start_date)->format('Y-m-d') : '') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Fecha de finalización <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $hackathon->end_date ? \Carbon\Carbon::parse($hackathon->end_date)->format('Y-m-d') : '') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <h6 class="mt-4 mb-3">Configuración de participantes</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="max_participants" class="form-label">Máximo de participantes <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_participants') is-invalid @enderror" id="max_participants" name="max_participants" value="{{ old('max_participants', $hackathon->max_participants) }}" min="5" max="500" required>
                                @error('max_participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="max_teams" class="form-label">Máximo de equipos <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_teams') is-invalid @enderror" id="max_teams" name="max_teams" value="{{ old('max_teams', $hackathon->max_teams) }}" min="1" max="100" required>
                                @error('max_teams')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="team_size" class="form-label">Tamaño del equipo <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('team_size') is-invalid @enderror" id="team_size" name="team_size" value="{{ old('team_size', $hackathon->team_size) }}" min="1" max="10" required>
                                @error('team_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            <a href="{{ route('hackathons.show', $hackathon->id) }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tarjeta para gestionar las rondas -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Gestión de Rondas</h5>
                </div>
                <div class="card-body">
                    <p>Administra las fases o rondas de tu hackathon. Cada ronda puede tener diferentes objetivos, fechas y entregables.</p>
                    <p><strong>Rondas actuales:</strong> {{ $hackathon->rounds->count() }}</p>
                    <a href="{{ route('teacher.hackathon.rounds', ['hackathon_id' => $hackathon->id]) }}" class="btn btn-success">
                        <i class="fas fa-flag-checkered me-2"></i>Gestionar Rondas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Validación de fechas
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        // Actualizar fecha mínima para finalización cuando cambia inicio
        startDateInput.addEventListener('change', function() {
            endDateInput.min = startDateInput.value;
            
            // Si la fecha de fin es anterior a la de inicio, actualizarla
            if (endDateInput.value && endDateInput.value < startDateInput.value) {
                endDateInput.value = startDateInput.value;
            }
        });
    });
</script>
@endsection 