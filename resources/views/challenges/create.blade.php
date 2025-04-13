@extends('layouts.app')

@section('title', 'Crear Desafío')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h2">Crear Nuevo Desafío</h1>
        <p class="text-muted">Crea un nuevo desafío para tus estudiantes</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('challenges.store') }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h5 class="card-title mb-3">Información General</h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Título del Desafío *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" maxlength="150" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Un título descriptivo para el desafío (máx. 150 caracteres)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Una breve descripción del desafío</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="objectives" class="form-label">Objetivos de Aprendizaje *</label>
                            <textarea class="form-control @error('objectives') is-invalid @enderror" id="objectives" name="objectives" rows="3" required>{{ old('objectives') }}</textarea>
                            @error('objectives')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Los objetivos que se espera que los estudiantes alcancen</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instrucciones Generales *</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="4" required>{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Instrucciones generales para completar el desafío</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <h5 class="card-title mb-3">Configuración</h5>

                        <div class="mb-3">
                            <label for="challenge_type" class="form-label">Tipo de Desafío *</label>
                            <select class="form-select @error('challenge_type') is-invalid @enderror" id="challenge_type" name="challenge_type" required>
                                <option value="python" {{ old('challenge_type') == 'python' ? 'selected' : '' }}>Python</option>
                                <option value="ai_prompt" {{ old('challenge_type') == 'ai_prompt' ? 'selected' : '' }}>Prompts de IA</option>
                            </select>
                            @error('challenge_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">El tipo de desafío determina el tipo de ejercicios</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="class_id" class="form-label">Asignar a Clase</label>
                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id">
                                <option value="">Seleccionar clase...</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->class_id }}" {{ old('class_id') == $classroom->class_id ? 'selected' : '' }}>{{ $classroom->class_name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_public">Disponible públicamente</label>
                            <div class="form-text">Si se marca, cualquier estudiante podrá acceder al desafío</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="difficulty" class="form-label">Nivel de Dificultad *</label>
                            <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                                <option value="principiante" {{ old('difficulty') == 'principiante' ? 'selected' : '' }}>Principiante</option>
                                <option value="intermedio" {{ old('difficulty') == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                                <option value="avanzado" {{ old('difficulty') == 'avanzado' ? 'selected' : '' }}>Avanzado</option>
                            </select>
                            @error('difficulty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="estimated_time" class="form-label">Tiempo Estimado (minutos)</label>
                            <input type="number" class="form-control @error('estimated_time') is-invalid @enderror" id="estimated_time" name="estimated_time" value="{{ old('estimated_time') }}" min="1">
                            @error('estimated_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="points" class="form-label">Puntos</label>
                            <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points', 100) }}" min="0">
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="card-title mb-3">Criterios de Evaluación (Opcional)</h5>
                        <div class="mb-3">
                            <textarea class="form-control @error('evaluation_criteria') is-invalid @enderror" id="evaluation_criteria" name="evaluation_criteria" rows="4" placeholder="Ingresa un criterio por línea...">{{ old('evaluation_criteria') }}</textarea>
                            @error('evaluation_criteria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Criterios para evaluar el desafío (uno por línea)</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="card-title mb-3">Guía de Solución (Opcional)</h5>
                        <div class="mb-3">
                            <textarea class="form-control @error('solution_guide') is-invalid @enderror" id="solution_guide" name="solution_guide" rows="4" placeholder="Notas solo visibles para el profesor...">{{ old('solution_guide') }}</textarea>
                            @error('solution_guide')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Guía de solución para el profesor (no visible para estudiantes)</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('challenges.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Crear Desafío</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar al menos una opción seleccionada (clase o público)
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const classId = document.getElementById('class_id').value;
            const isPublic = document.getElementById('is_public').checked;
            
            if (!classId && !isPublic) {
                e.preventDefault();
                alert('Debes asignar el desafío a una clase o marcarlo como público.');
            }
        });
    });
</script>
@endpush 