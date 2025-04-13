@extends('layouts.app')

@section('title', 'Mis Clases')

@section('header', 'Mis Clases')

@section('content')
    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold mb-0">Listado de Clases</h2>
        <a href="{{ route('classrooms.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Nueva Clase
        </a>
    </div>

    @if(count($classrooms) > 0)
        <div class="row">
            @foreach($classrooms as $classroom)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-{{ $classroom->is_active ? 'primary' : 'secondary' }}">
                        <div class="card-header bg-{{ $classroom->is_active ? 'primary' : 'secondary' }} text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $classroom->name }}</h5>
                            <span class="badge bg-light text-{{ $classroom->is_active ? 'primary' : 'secondary' }}">
                                {{ $classroom->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $classroom->description ?: 'Sin descripción' }}</p>
                            <p class="mb-1"><strong>Código de acceso:</strong> <span class="badge bg-secondary">{{ $classroom->access_code }}</span></p>
                            <p class="mb-0"><strong>Estudiantes:</strong> {{ $classroom->enrollments->count() }}</p>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-end">
                            <a href="{{ route('classrooms.show', $classroom->class_id) }}" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="{{ route('classrooms.edit', $classroom->class_id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <p class="mb-0">No tienes clases creadas aún. Haz clic en "Nueva Clase" para crear tu primera clase.</p>
        </div>
    @endif
@endsection 