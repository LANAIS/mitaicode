@extends('layouts.app')

@section('title', 'Editar Desafío')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h2">Editar Desafío</h1>
        <p class="text-muted">Actualiza la información y configuración del desafío</p>
    </div>

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

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('challenges.update', $challenge->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h5 class="card-title mb-3">Información General</h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Título del Desafío *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $challenge->title) }}" maxlength="150" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Un título descriptivo para el desafío (máx. 150 caracteres)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description', $challenge->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Una breve descripción del desafío</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="objectives" class="form-label">Objetivos de Aprendizaje *</label>
                            <textarea class="form-control @error('objectives') is-invalid @enderror" id="objectives" name="objectives" rows="3" required>{{ old('objectives', $challenge->objectives) }}</textarea>
                            @error('objectives')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Los objetivos que se espera que los estudiantes alcancen</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instrucciones Generales *</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="4" required>{{ old('instructions', $challenge->instructions) }}</textarea>
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
                                <option value="python" {{ old('challenge_type', $challenge->challenge_type) == 'python' ? 'selected' : '' }}>Python</option>
                                <option value="ai_prompt" {{ old('challenge_type', $challenge->challenge_type) == 'ai_prompt' ? 'selected' : '' }}>Prompts de IA</option>
                            </select>
                            @error('challenge_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">El tipo de desafío determina el tipo de ejercicios</div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Estado *</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="draft" {{ old('status', $challenge->status) == 'draft' ? 'selected' : '' }}>Borrador</option>
                                <option value="published" {{ old('status', $challenge->status) == 'published' ? 'selected' : '' }}>Publicado</option>
                                <option value="archived" {{ old('status', $challenge->status) == 'archived' ? 'selected' : '' }}>Archivado</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">El estado actual del desafío</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="class_id" class="form-label">Asignar a Clase</label>
                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id">
                                <option value="">Seleccionar clase...</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->class_id }}" {{ old('class_id', $challenge->class_id) == $classroom->class_id ? 'selected' : '' }}>{{ $classroom->class_name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public', $challenge->is_public) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_public">Disponible públicamente</label>
                            <div class="form-text">Si se marca, cualquier estudiante podrá acceder al desafío</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="difficulty" class="form-label">Nivel de Dificultad *</label>
                            <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                                <option value="principiante" {{ old('difficulty', $challenge->difficulty) == 'principiante' ? 'selected' : '' }}>Principiante</option>
                                <option value="intermedio" {{ old('difficulty', $challenge->difficulty) == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                                <option value="avanzado" {{ old('difficulty', $challenge->difficulty) == 'avanzado' ? 'selected' : '' }}>Avanzado</option>
                            </select>
                            @error('difficulty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="estimated_time" class="form-label">Tiempo Estimado (minutos)</label>
                            <input type="number" class="form-control @error('estimated_time') is-invalid @enderror" id="estimated_time" name="estimated_time" value="{{ old('estimated_time', $challenge->estimated_time) }}" min="1">
                            @error('estimated_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="points" class="form-label">Puntos</label>
                            <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points', $challenge->points) }}" min="0">
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Orden</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $challenge->order) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Orden de aparición dentro de la clase (menor primero)</div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="card-title mb-3">Criterios de Evaluación (Opcional)</h5>
                        <div class="mb-3">
                            <textarea class="form-control @error('evaluation_criteria') is-invalid @enderror" id="evaluation_criteria" name="evaluation_criteria" rows="4" placeholder="Ingresa un criterio por línea...">{{ old('evaluation_criteria', is_array($challenge->evaluation_criteria) ? implode("\n", $challenge->evaluation_criteria) : $challenge->evaluation_criteria) }}</textarea>
                            @error('evaluation_criteria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Criterios para evaluar el desafío (uno por línea)</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="card-title mb-3">Guía de Solución (Opcional)</h5>
                        <div class="mb-3">
                            <textarea class="form-control @error('solution_guide') is-invalid @enderror" id="solution_guide" name="solution_guide" rows="4" placeholder="Notas solo visibles para el profesor...">{{ old('solution_guide', $challenge->solution_guide) }}</textarea>
                            @error('solution_guide')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Guía de solución para el profesor (no visible para estudiantes)</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('challenges.index') }}" class="btn btn-secondary">Volver</a>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Ejercicios del Desafío</h5>
            <div>
                <a href="{{ route('challenges.exercises.create', $challenge->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Añadir Ejercicio
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($exercises->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-tasks fa-3x text-muted"></i>
                    </div>
                    <h5>No hay ejercicios</h5>
                    <p class="text-muted">Añade ejercicios para que los estudiantes puedan completar este desafío.</p>
                    <a href="{{ route('challenges.exercises.create', $challenge->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Añadir primer ejercicio
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="40%">Título</th>
                                <th width="15%">Puntos</th>
                                <th width="15%">Dificultad</th>
                                <th width="25%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="exercises-list">
                            @foreach($exercises as $exercise)
                                <tr data-id="{{ $exercise->id }}" data-order="{{ $exercise->order }}">
                                    <td>{{ $exercise->order + 1 }}</td>
                                    <td>{{ $exercise->title }}</td>
                                    <td>{{ $exercise->points }}</td>
                                    <td>
                                        @if($exercise->difficulty == 'principiante')
                                            <span class="badge bg-success">Principiante</span>
                                        @elseif($exercise->difficulty == 'intermedio')
                                            <span class="badge bg-warning text-dark">Intermedio</span>
                                        @else
                                            <span class="badge bg-danger">Avanzado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('challenges.exercises.submissions', $exercise->id) }}" class="btn btn-outline-info">
                                                <i class="fas fa-clipboard-list"></i> Entregas
                                            </a>
                                            <a href="{{ route('challenges.exercises.edit', $exercise->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <form action="/teaching-challenges/exercises/{{ $exercise->id }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar este ejercicio?');">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
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