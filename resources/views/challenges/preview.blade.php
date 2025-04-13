@extends('layouts.app')

@section('title', 'Vista Previa: ' . $challenge->title)

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">Vista Previa: {{ $challenge->title }}</h2>
            <p class="text-muted">Así es como los estudiantes verán este desafío</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('challenges.edit', $challenge->id) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-arrow-left me-2"></i> Volver a Editar
            </a>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i> Esta es una vista previa de cómo verán los estudiantes este desafío. Los estudiantes no podrán verlo hasta que lo publiques.
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="challenge-header mb-4">
                        <span class="badge bg-{{ $challenge->challenge_type === 'python' ? 'success' : 'primary' }} me-2">
                            {{ $challenge->challenge_type === 'python' ? 'Python' : 'Prompts IA' }}
                        </span>
                        <span class="badge bg-secondary">
                            {{ $challenge->difficulty === 'easy' ? 'Fácil' : ($challenge->difficulty === 'medium' ? 'Intermedio' : 'Avanzado') }}
                        </span>
                    </div>
                    
                    <div class="challenge-content mb-4">
                        <h4>Descripción</h4>
                        <div class="mb-4">
                            {!! nl2br(e($challenge->description)) !!}
                        </div>
                        
                        <h4>Objetivos de Aprendizaje</h4>
                        <div class="mb-4">
                            {!! nl2br(e($challenge->learning_objectives)) !!}
                        </div>
                        
                        <h4>Instrucciones</h4>
                        <div>
                            {!! nl2br(e($challenge->instructions)) !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ejercicios del Desafío</h5>
                </div>
                <div class="card-body">
                    @if($challenge->exercises->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No hay ejercicios en este desafío todavía.</p>
                            <a href="{{ route('challenges.exercises.create', $challenge->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Añadir Ejercicio
                            </a>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($challenge->exercises as $exercise)
                                <div class="list-group-item border-0 py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $exercise->title }}</h6>
                                            <p class="text-muted small mb-0">
                                                {{ Str::limit($exercise->description, 100) }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="badge bg-secondary">Ejercicio {{ $loop->iteration }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Desafío</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-clock me-2 text-muted"></i> Duración Estimada</span>
                            <span>{{ $challenge->estimated_time }} minutos</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-trophy me-2 text-muted"></i> Puntos</span>
                            <span>{{ $challenge->points }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-tasks me-2 text-muted"></i> Ejercicios</span>
                            <span>{{ $challenge->exercises->count() }}</span>
                        </li>
                        @if($challenge->class_id)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="fas fa-users me-2 text-muted"></i> Clase</span>
                                <span>{{ $challenge->classroom ? $challenge->classroom->name : 'N/A' }}</span>
                            </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-eye me-2 text-muted"></i> Visibilidad</span>
                            <span>{{ $challenge->is_public ? 'Público' : 'Solo clase' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Estado del Desafío</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <span class="badge bg-{{ $challenge->status === 'draft' ? 'warning' : ($challenge->status === 'published' ? 'success' : 'danger') }} p-2 w-100">
                            {{ $challenge->status === 'draft' ? 'Borrador' : ($challenge->status === 'published' ? 'Publicado' : 'Archivado') }}
                        </span>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('challenges.edit', $challenge->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar Desafío
                        </a>
                        @if($challenge->status === 'draft')
                            <form action="{{ route('challenges.status', $challenge->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="published">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle me-1"></i> Publicar Desafío
                                </button>
                            </form>
                        @elseif($challenge->status === 'published')
                            <form action="{{ route('challenges.status', $challenge->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="draft">
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-edit me-1"></i> Volver a Borrador
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 