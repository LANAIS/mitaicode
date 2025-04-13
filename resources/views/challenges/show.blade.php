@extends('layouts.app')

@section('title', $challenge->title)

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<div class="container py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('challenges.index') }}">Desafíos</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $challenge->title }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h2">{{ $challenge->title }}</h1>
            <div>
                <span class="badge bg-{{ $challenge->challenge_type == 'python' ? 'primary' : 'success' }} py-2 px-3 me-2">
                    {{ $challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
                </span>
                <span class="badge bg-{{ $challenge->status == 'published' ? 'success' : ($challenge->status == 'draft' ? 'secondary' : 'warning') }}">
                    {{ $challenge->status == 'published' ? 'Publicado' : ($challenge->status == 'draft' ? 'Borrador' : 'Archivado') }}
                </span>
            </div>
        </div>
        
        <div class="badge bg-{{ $challenge->difficulty == 'principiante' ? 'success' : ($challenge->difficulty == 'intermedio' ? 'primary' : 'danger') }} mb-2">
            {{ ucfirst($challenge->difficulty) }}
        </div>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Descripción</h5>
                    <p>{{ $challenge->description }}</p>
                    
                    <h5 class="card-title mt-4">Objetivos</h5>
                    <p>{{ $challenge->objectives }}</p>
                    
                    <h5 class="card-title mt-4">Instrucciones</h5>
                    <p>{{ $challenge->instructions }}</p>

                    @if($challenge->solution_guide)
                    <h5 class="card-title mt-4">Guía de Solución</h5>
                    <p>{{ $challenge->solution_guide }}</p>
                    @endif
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ejercicios</h5>
                    <a href="{{ route('challenges.exercises.create', $challenge->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Añadir Ejercicio
                    </a>
                </div>
                <div class="card-body">
                    @if($challenge->exercises->isEmpty())
                        <div class="alert alert-info">
                            Este desafío aún no tiene ejercicios. Añade ejercicios para que los estudiantes puedan completar el desafío.
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($challenge->exercises as $exercise)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $exercise->title }}</h6>
                                        <small class="text-muted">{{ Str::limit($exercise->description, 100) }}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-2">{{ $exercise->points }} pts</span>
                                        <a href="{{ route('challenges.exercises.edit', $exercise->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('challenges.exercises.destroy', $exercise->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este ejercicio?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
                <div class="card-body">
                    <h5 class="card-title">Información del Desafío</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-clock me-2"></i> Tiempo estimado:</span>
                            <span class="fw-bold">{{ $challenge->estimated_time ? $challenge->estimated_time . ' min' : 'No especificado' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-trophy me-2"></i> Puntos:</span>
                            <span class="fw-bold">{{ $challenge->points }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-tasks me-2"></i> Ejercicios:</span>
                            <span class="fw-bold">{{ $challenge->exercises->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-graduation-cap me-2"></i> Clase:</span>
                            <span class="fw-bold">{{ $challenge->classroom && $challenge->classroom->class_name ? $challenge->classroom->class_name : 'Público' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-user me-2"></i> Creado por:</span>
                            <span class="fw-bold">{{ $challenge->teacher->first_name }} {{ $challenge->teacher->last_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-calendar-alt me-2"></i> Creado:</span>
                            <span class="fw-bold">{{ $challenge->created_at->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-calendar-check me-2"></i> Actualizado:</span>
                            <span class="fw-bold">{{ $challenge->updated_at->format('d/m/Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Acciones</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('challenges.edit', $challenge->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar Desafío
                        </a>
                        <a href="{{ route('challenges.preview', $challenge->id) }}" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i> Vista Previa
                        </a>
                        @if($challenge->status == 'draft')
                            <form action="{{ route('challenges.status', $challenge->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="published">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle me-1"></i> Publicar Desafío
                                </button>
                            </form>
                        @elseif($challenge->status == 'published')
                            <form action="{{ route('challenges.status', $challenge->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="archived">
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-archive me-1"></i> Archivar Desafío
                                </button>
                            </form>
                        @else
                            <form action="{{ route('challenges.status', $challenge->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="published">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check-circle me-1"></i> Restaurar Desafío
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('challenges.analytics', $challenge->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-bar me-1"></i> Ver Analíticas
                        </a>
                        
                        <form action="{{ route('challenges.destroy', $challenge->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este desafío? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i> Eliminar Desafío
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="d-grid">
                <a href="{{ route('challenges.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver a la lista
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 