@extends('layouts.app')

@section('title', 'Crear Nuevo Hackathon')

@section('header', 'Crear Nuevo Hackathon')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('hackathons.index') }}">Hackathones</a></li>
                    <li class="breadcrumb-item active">Crear Nuevo Hackathon</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información del Hackathon</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hackathons.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagen del Hackathon</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                            <div class="form-text">Formato recomendado: JPG/PNG, máximo 2MB</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Fecha de finalización <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <h6 class="mt-4 mb-3">Configuración de participantes</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="max_participants" class="form-label">Máximo de participantes <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_participants') is-invalid @enderror" id="max_participants" name="max_participants" value="{{ old('max_participants', 100) }}" min="5" max="500" required>
                                @error('max_participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="max_teams" class="form-label">Máximo de equipos <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_teams') is-invalid @enderror" id="max_teams" name="max_teams" value="{{ old('max_teams', 20) }}" min="1" max="100" required>
                                @error('max_teams')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="team_size" class="form-label">Tamaño del equipo <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('team_size') is-invalid @enderror" id="team_size" name="team_size" value="{{ old('team_size', 5) }}" min="1" max="10" required>
                                @error('team_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Sección de Rondas/Fases -->
                        <h6 class="mt-4 mb-3">Rondas/Fases del Hackathon</h6>
                        
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <p class="mb-0">Configure las rondas o fases de su hackathon. Cada ronda puede tener objetivos específicos y fechas de inicio/fin. Podrá añadir más rondas después de crear el hackathon.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div id="rounds-container">
                            <div class="round-item card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Ronda 1</h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-round-btn" style="display: none;"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre de la ronda <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="rounds[0][name]" value="{{ old('rounds.0.name', 'Fase Inicial') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea class="form-control" name="rounds[0][description]" rows="2">{{ old('rounds.0.description', 'Fase inicial del hackathon') }}</textarea>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control round-start-date" name="rounds[0][start_date]" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Fecha de finalización <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control round-end-date" name="rounds[0][end_date]" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Objetivos de la ronda</label>
                                        <textarea class="form-control" name="rounds[0][objectives]" rows="2">{{ old('rounds.0.objectives', 'Definir idea y formar equipos') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Entregables esperados</label>
                                        <textarea class="form-control" name="rounds[0][deliverables]" rows="2">{{ old('rounds.0.deliverables', 'Documento de propuesta inicial') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="button" id="add-round-btn" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Añadir otra ronda
                            </button>
                        </div>
                        
                        <!-- Sección de Jurados -->
                        <h6 class="mt-4 mb-3">Jurados</h6>
                        
                        <div class="alert alert-info">
                            <p class="mb-0">Podrá asignar jurados después de crear el hackathon.</p>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('hackathons.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear Hackathon</button>
                        </div>
                    </form>
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
        
        // Establecer fecha mínima para inicio (hoy)
        const today = new Date().toISOString().split('T')[0];
        startDateInput.min = today;
        
        // Actualizar fecha mínima para finalización cuando cambia inicio
        startDateInput.addEventListener('change', function() {
            endDateInput.min = startDateInput.value;
            updateRoundDates();
            
            // Si la fecha de fin es anterior a la de inicio, actualizarla
            if (endDateInput.value && endDateInput.value < startDateInput.value) {
                endDateInput.value = startDateInput.value;
            }
        });
        
        endDateInput.addEventListener('change', function() {
            updateRoundDates();
        });
        
        // Configuración inicial de fechas de rondas
        updateRoundDates();
        
        // Sistema de gestión de rondas
        let roundCount = 1;
        const roundsContainer = document.getElementById('rounds-container');
        const addRoundBtn = document.getElementById('add-round-btn');
        
        // Añadir nueva ronda
        addRoundBtn.addEventListener('click', function() {
            roundCount++;
            const newRound = createRoundElement(roundCount);
            roundsContainer.appendChild(newRound);
            
            // Actualizar las fechas de las rondas
            updateRoundDates();
            
            // Mostrar botones de eliminar si hay más de una ronda
            if (roundCount > 1) {
                document.querySelectorAll('.remove-round-btn').forEach(btn => {
                    btn.style.display = 'block';
                });
            }
        });
        
        // Eliminar ronda (delegación de eventos)
        roundsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-round-btn') || e.target.parentElement.classList.contains('remove-round-btn')) {
                const roundItem = e.target.closest('.round-item');
                roundItem.remove();
                
                // Renumerar las rondas visualmente
                document.querySelectorAll('.round-item').forEach((item, index) => {
                    item.querySelector('h6').textContent = `Ronda ${index + 1}`;
                });
                
                roundCount--;
                
                // Ocultar botones de eliminar si solo queda una ronda
                if (roundCount <= 1) {
                    document.querySelectorAll('.remove-round-btn').forEach(btn => {
                        btn.style.display = 'none';
                    });
                }
                
                // Reenumerar los índices en los nombres de los campos
                renumberRounds();
            }
        });
        
        // Funciones auxiliares
        function createRoundElement(index) {
            const roundItem = document.createElement('div');
            roundItem.className = 'round-item card mb-3';
            roundItem.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Ronda ${index}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-round-btn"><i class="fas fa-times"></i></button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la ronda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="rounds[${index-1}][name]" value="Fase ${index}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="rounds[${index-1}][description]" rows="2"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control round-start-date" name="rounds[${index-1}][start_date]" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de finalización <span class="text-danger">*</span></label>
                            <input type="date" class="form-control round-end-date" name="rounds[${index-1}][end_date]" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Objetivos de la ronda</label>
                        <textarea class="form-control" name="rounds[${index-1}][objectives]" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Entregables esperados</label>
                        <textarea class="form-control" name="rounds[${index-1}][deliverables]" rows="2"></textarea>
                    </div>
                </div>
            `;
            return roundItem;
        }
        
        function updateRoundDates() {
            const hackathonStart = startDateInput.value;
            const hackathonEnd = endDateInput.value;
            
            if (!hackathonStart || !hackathonEnd) return;
            
            const startDate = new Date(hackathonStart);
            const endDate = new Date(hackathonEnd);
            const totalDays = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24));
            
            const roundItems = document.querySelectorAll('.round-item');
            const roundCount = roundItems.length;
            
            if (roundCount <= 0 || totalDays <= 0) return;
            
            // Distribuir los días disponibles entre las rondas
            const daysPerRound = Math.floor(totalDays / roundCount);
            
            roundItems.forEach((round, index) => {
                const startInput = round.querySelector('.round-start-date');
                const endInput = round.querySelector('.round-end-date');
                
                const roundStartDate = new Date(startDate);
                roundStartDate.setDate(startDate.getDate() + (daysPerRound * index));
                
                const roundEndDate = new Date(roundStartDate);
                if (index === roundCount - 1) {
                    // La última ronda termina en la fecha final del hackathon
                    roundEndDate.setTime(endDate.getTime());
                } else {
                    roundEndDate.setDate(roundStartDate.getDate() + daysPerRound - 1);
                }
                
                startInput.value = roundStartDate.toISOString().split('T')[0];
                endInput.value = roundEndDate.toISOString().split('T')[0];
                
                // Establecer mínimos y máximos
                startInput.min = hackathonStart;
                startInput.max = hackathonEnd;
                endInput.min = startInput.value;
                endInput.max = hackathonEnd;
            });
        }
        
        function renumberRounds() {
            const roundItems = document.querySelectorAll('.round-item');
            
            roundItems.forEach((item, index) => {
                const inputs = item.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/rounds\[\d+\]/, `rounds[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }
    });
</script>
@endsection 