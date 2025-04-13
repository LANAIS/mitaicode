@extends('layouts.app')

@section('title', 'Añadir Ejercicio')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h2">Añadir Ejercicio</h1>
        <p class="text-muted">Desafío: {{ $challenge->title }}</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('challenges.exercises.store', $challenge->id) }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <h5 class="card-title mb-3">Información del Ejercicio</h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Título del Ejercicio *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" maxlength="150" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Una breve descripción del ejercicio (opcional)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instrucciones *</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="4" required>{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Instrucciones detalladas para completar el ejercicio</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="hints" class="form-label">Pistas</label>
                            <textarea class="form-control @error('hints') is-invalid @enderror" id="hints" name="hints" rows="3">{{ old('hints') }}</textarea>
                            @error('hints')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Pistas que ayudarán a los estudiantes (opcional)</div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <h5 class="card-title mb-3">Configuración</h5>
                        
                        <div class="mb-3">
                            <label for="order" class="form-label">Orden</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $challenge->exercises()->count()) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Posición del ejercicio (0 = primero)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="points" class="form-label">Puntos</label>
                            <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points', 10) }}" min="0">
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Puntos otorgados al completar</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="difficulty" class="form-label">Nivel de Dificultad</label>
                            <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty">
                                <option value="principiante" {{ old('difficulty') == 'principiante' ? 'selected' : '' }}>Principiante</option>
                                <option value="intermedio" {{ old('difficulty') == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                                <option value="avanzado" {{ old('difficulty') == 'avanzado' ? 'selected' : '' }}>Avanzado</option>
                            </select>
                            @error('difficulty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Nivel de dificultad del ejercicio</div>
                        </div>
                    </div>
                </div>
                
                @if($challenge->challenge_type === 'python')
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="card-title mb-3">Código Python</h5>
                        
                        <div class="mb-3">
                            <label for="starter_code" class="form-label">Código Inicial</label>
                            <textarea class="form-control @error('starter_code') is-invalid @enderror" id="starter_code" name="starter_code" rows="6">{{ old('starter_code') }}</textarea>
                            @error('starter_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Código inicial que verá el estudiante (opcional)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="solution_code" class="form-label">Código Solución *</label>
                            <textarea class="form-control @error('solution_code') is-invalid @enderror" id="solution_code" name="solution_code" rows="8" required>{{ old('solution_code') }}</textarea>
                            @error('solution_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Solución correcta del ejercicio (no visible para estudiantes)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="test_cases" class="form-label">Casos de Prueba *</label>
                            <textarea class="form-control @error('test_cases') is-invalid @enderror" id="test_cases" name="test_cases" rows="8" required>{{ old('test_cases') }}</textarea>
                            @error('test_cases')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Define casos de prueba (un caso por línea) en formato: <code>entrada >>> salida_esperada</code><br>
                                Ejemplo: <code>5, 10 >>> 15</code> para una función que suma dos números
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($challenge->challenge_type === 'ai_prompt')
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="card-title mb-3">Prompt de IA</h5>
                        
                        <div class="mb-3">
                            <label for="example_prompt" class="form-label">Prompt Ejemplo *</label>
                            <textarea class="form-control @error('example_prompt') is-invalid @enderror" id="example_prompt" name="example_prompt" rows="10" required>{{ old('example_prompt') }}</textarea>
                            @error('example_prompt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Un ejemplo de prompt bien estructurado que sirva como referencia</div>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('challenges.edit', $challenge->id) }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Ejercicio</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 