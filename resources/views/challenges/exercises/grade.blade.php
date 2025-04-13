@extends('layouts.app')

@section('title', 'Calificar Entrega')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('challenges.index') }}">Desafíos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('challenges.edit', $submission->exercise->challenge->id) }}">{{ $submission->exercise->challenge->title }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('challenges.exercises.submissions', $submission->exercise->id) }}">Entregas de {{ $submission->exercise->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Calificar Entrega #{{ $submission->id }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Calificar Entrega</h1>
            <span class="badge bg-{{ $submission->exercise->challenge->challenge_type == 'python' ? 'primary' : 'success' }} py-2 px-3">
                {{ $submission->exercise->challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
            </span>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Contenido de la entrega -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $submission->exercise->challenge->challenge_type == 'python' ? 'Código Enviado' : 'Prompt Enviado' }}</h5>
                    <small class="text-muted">{{ $submission->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded">{{ $submission->exercise->challenge->challenge_type == 'python' ? $submission->submitted_code : $submission->submitted_prompt }}</pre>
                </div>
            </div>
            
            @if($submission->exercise->challenge->challenge_type == 'python' && !empty($submission->exercise->solution_code))
            <!-- Solución del ejercicio -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Solución de Referencia</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded">{{ $submission->exercise->solution_code }}</pre>
                </div>
            </div>
            @endif
            
            <!-- Formulario de calificación -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Formulario de Calificación</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('challenges.submissions.submit_grade', $submission->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="score" class="form-label">Calificación (0-100)</label>
                            <div class="d-flex align-items-center">
                                <input type="range" class="form-range flex-grow-1 me-2" min="0" max="100" step="5" id="score" name="score" value="{{ $submission->status == 'graded' ? $submission->score : 75 }}" oninput="updateScoreValue(this.value)">
                                <span class="score-value badge bg-primary py-2 px-3 fs-6" id="scoreValue">{{ $submission->status == 'graded' ? $submission->score : 75 }}</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="feedback" class="form-label">Retroalimentación</label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="6" required>{{ $submission->status == 'graded' ? $submission->feedback : '' }}</textarea>
                            <div class="form-text">Proporcione retroalimentación constructiva que ayude al estudiante a mejorar.</div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('challenges.exercises.submissions', $submission->exercise->id) }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Calificación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Información del estudiante -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Estudiante</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($submission->student->avatar_url)
                            <img src="{{ $submission->student->avatar_url }}" alt="{{ $submission->student->first_name }}" class="rounded-circle me-3" width="64" height="64">
                        @else
                            <div class="avatar-placeholder me-3">
                                {{ strtoupper(substr($submission->student->first_name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $submission->student->first_name }} {{ $submission->student->last_name }}</h6>
                            <p class="text-muted mb-0">{{ $submission->student->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información del ejercicio -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Ejercicio</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $submission->exercise->title }}</h6>
                    <p class="text-muted">{{ Str::limit($submission->exercise->description, 100) }}</p>
                    
                    <hr>
                    
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-tasks me-2"></i> Desafío:</span>
                            <span>{{ $submission->exercise->challenge->title }}</span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-clock me-2"></i> Enviado:</span>
                            <span>{{ $submission->created_at->diffForHumans() }}</span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-hashtag me-2"></i> Intento:</span>
                            <span>#{{ $submission->attempt_number }}</span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted"><i class="fas fa-trophy me-2"></i> Puntos del ejercicio:</span>
                            <span>{{ $submission->exercise->points }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            @if($submission->status == 'graded')
            <!-- Estado actual de la calificación -->
            <div class="card shadow-sm mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Calificación Actual</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Puntuación</h6>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                <div class="progress-bar bg-{{ $submission->score >= 70 ? 'success' : ($submission->score >= 50 ? 'warning' : 'danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $submission->score }}%;" 
                                     aria-valuenow="{{ $submission->score }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <span class="fw-bold">{{ $submission->score }}/100</span>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <h6>Feedback Actual</h6>
                        <div class="p-2 border rounded">
                            <p class="mb-0 small">{{ $submission->feedback }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateScoreValue(val) {
        document.getElementById('scoreValue').innerText = val;
        
        // Cambiar el color según el valor
        const badge = document.getElementById('scoreValue');
        if (val >= 70) {
            badge.className = 'score-value badge bg-success py-2 px-3 fs-6';
        } else if (val >= 50) {
            badge.className = 'score-value badge bg-warning py-2 px-3 fs-6';
        } else {
            badge.className = 'score-value badge bg-danger py-2 px-3 fs-6';
        }
    }
</script>
@endsection

@section('styles')
<style>
.avatar-placeholder {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background-color: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
</style>
@endsection 