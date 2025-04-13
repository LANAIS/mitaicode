@extends('layouts.app')

@section('title', 'Entregas de ' . $exercise->title)

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('challenges.index') }}">Desafíos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('challenges.edit', $exercise->challenge->id) }}">{{ $exercise->challenge->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Entregas de {{ $exercise->title }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Entregas de {{ $exercise->title }}</h1>
            <span class="badge bg-{{ $exercise->challenge->challenge_type == 'python' ? 'primary' : 'success' }} py-2 px-3">
                {{ $exercise->challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
            </span>
        </div>
    </div>
    
    <!-- Información del ejercicio -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Información del ejercicio</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>{{ $exercise->title }}</h5>
                    <p>{{ $exercise->description }}</p>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <span class="text-muted"><i class="fas fa-layer-group me-2"></i> Desafío:</span>
                                <span class="fw-bold">{{ $exercise->challenge->title }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted"><i class="fas fa-sort-numeric-up me-2"></i> Orden:</span>
                                <span>{{ $exercise->order + 1 }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted"><i class="fas fa-trophy me-2"></i> Puntos:</span>
                                <span>{{ $exercise->points }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted"><i class="fas fa-list-alt me-2"></i> Entregas:</span>
                                <span>{{ $submissions->total() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lista de entregas -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Lista de entregas</h5>
        </div>
        <div class="card-body">
            @if($submissions->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i> No hay entregas para este ejercicio aún.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante</th>
                                <th>Fecha de entrega</th>
                                <th>Intento</th>
                                <th>Estado</th>
                                <th>Calificación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                                <tr>
                                    <td>{{ $submission->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($submission->student->avatar_url)
                                                <img src="{{ $submission->student->avatar_url }}" alt="{{ $submission->student->first_name }}" class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <div class="avatar-placeholder me-2">{{ strtoupper(substr($submission->student->first_name, 0, 1)) }}</div>
                                            @endif
                                            <span>{{ $submission->student->first_name }} {{ $submission->student->last_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $submission->created_at->format('d/m/Y H:i') }}</td>
                                    <td>#{{ $submission->attempt_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $submission->status == 'graded' ? 'success' : 'secondary' }}">
                                            {{ $submission->status == 'graded' ? 'Calificado' : 'Pendiente' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($submission->status == 'graded')
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px; width: 80px;">
                                                    <div class="progress-bar bg-{{ $submission->score >= 70 ? 'success' : ($submission->score >= 50 ? 'warning' : 'danger') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $submission->score }}%;" 
                                                         aria-valuenow="{{ $submission->score }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <span>{{ $submission->score }}/100</span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('challenges.submissions.grade', $submission->id) }}" class="btn btn-sm btn-{{ $submission->status == 'graded' ? 'outline-primary' : 'primary' }}">
                                                <i class="fas fa-{{ $submission->status == 'graded' ? 'edit' : 'check' }} me-1"></i>
                                                {{ $submission->status == 'graded' ? 'Editar calificación' : 'Calificar' }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Botones de acción -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('challenges.edit', $exercise->challenge->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al desafío
        </a>
    </div>
</div>
@endsection

@section('styles')
<style>
.avatar-placeholder {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
</style>
@endsection 