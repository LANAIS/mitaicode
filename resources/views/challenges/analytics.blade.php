@extends('layouts.app')

@section('title', 'Análisis: ' . $challenge->title)

@section('styles')
<style>
    .metric-card {
        transition: all 0.3s;
    }
    .metric-card:hover {
        transform: translateY(-5px);
    }
    .progress-bar-sm {
        height: 5px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">Análisis: {{ $challenge->title }}</h2>
            <p class="text-muted">Estadísticas y rendimiento del desafío</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('challenges.edit', $challenge->id) }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-arrow-left me-2"></i> Volver al Desafío
            </a>
            <form action="{{ route('challenges.analytics.update', $challenge->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-1"></i> Actualizar Datos
                </button>
            </form>
        </div>
    </div>

    <!-- Métricas principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100 metric-card border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Estudiantes Totales</h6>
                    <h2 class="mb-0">{{ $analytics->total_students ?? 0 }}</h2>
                    <div class="text-muted small">con acceso al desafío</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 metric-card border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Han Iniciado</h6>
                    <h2 class="mb-0">{{ $analytics->started_count ?? 0 }}</h2>
                    <div class="progress progress-bar-sm mt-2 mb-1">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: {{ $analytics->total_students > 0 ? ($analytics->started_count / $analytics->total_students) * 100 : 0 }}%" 
                             aria-valuenow="{{ $analytics->started_count ?? 0 }}" aria-valuemin="0" aria-valuemax="{{ $analytics->total_students ?? 0 }}">
                        </div>
                    </div>
                    <div class="text-muted small">
                        {{ $analytics->total_students > 0 ? round(($analytics->started_count / $analytics->total_students) * 100) : 0 }}% del total
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 metric-card border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Han Completado</h6>
                    <h2 class="mb-0">{{ $analytics->completed_count ?? 0 }}</h2>
                    <div class="progress progress-bar-sm mt-2 mb-1">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $analytics->total_students > 0 ? ($analytics->completed_count / $analytics->total_students) * 100 : 0 }}%" 
                             aria-valuenow="{{ $analytics->completed_count ?? 0 }}" aria-valuemin="0" aria-valuemax="{{ $analytics->total_students ?? 0 }}">
                        </div>
                    </div>
                    <div class="text-muted small">
                        {{ $analytics->total_students > 0 ? round(($analytics->completed_count / $analytics->total_students) * 100) : 0 }}% del total
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100 metric-card border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Puntuación Media</h6>
                    <h2 class="mb-0">{{ round($analytics->average_score ?? 0) }}</h2>
                    <div class="progress progress-bar-sm mt-2 mb-1">
                        <div class="progress-bar {{ ($analytics->average_score ?? 0) >= 70 ? 'bg-success' : (($analytics->average_score ?? 0) >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                             role="progressbar" style="width: {{ $analytics->average_score ?? 0 }}%" 
                             aria-valuenow="{{ $analytics->average_score ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="text-muted small">de 100 puntos posibles</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Progreso de los estudiantes -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Progreso de los Estudiantes</h5>
                </div>
                <div class="card-body">
                    @if($challenge->studentProgress->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">Aún no hay datos de progreso de estudiantes.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Estado</th>
                                        <th>Progreso</th>
                                        <th>Última Actividad</th>
                                        <th>Puntuación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($challenge->studentProgress as $progress)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($progress->student->avatar_url)
                                                        <img src="{{ $progress->student->avatar_url }}" alt="{{ $progress->student->username }}" class="rounded-circle me-2" width="32" height="32">
                                                    @else
                                                        <div class="avatar bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                            <span class="text-white">{{ substr($progress->student->username, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                    <span>{{ $progress->student->first_name }} {{ $progress->student->last_name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($progress->status === 'completed')
                                                    <span class="badge bg-success">Completado</span>
                                                @elseif($progress->status === 'in_progress')
                                                    <span class="badge bg-primary">En Progreso</span>
                                                @else
                                                    <span class="badge bg-secondary">No Iniciado</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" 
                                                         style="width: {{ $progress->total_exercises > 0 ? ($progress->completed_exercises / $progress->total_exercises) * 100 : 0 }}%" 
                                                         aria-valuenow="{{ $progress->completed_exercises }}" aria-valuemin="0" aria-valuemax="{{ $progress->total_exercises }}">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $progress->completed_exercises }}/{{ $progress->total_exercises }} ejercicios</small>
                                            </td>
                                            <td>
                                                @if($progress->last_activity_at)
                                                    <span title="{{ $progress->last_activity_at->format('d/m/Y H:i') }}">
                                                        {{ $progress->last_activity_at->diffForHumans() }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($progress->status === 'completed')
                                                    <span class="badge {{ $progress->score >= 70 ? 'bg-success' : ($progress->score >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ $progress->score }}/100
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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

        <!-- Estadísticas adicionales -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Información del Desafío</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-code me-2 text-muted"></i> Tipo</span>
                            <span class="badge bg-{{ $challenge->challenge_type === 'python' ? 'success' : 'primary' }}">
                                {{ $challenge->challenge_type === 'python' ? 'Python' : 'Prompts IA' }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-signal me-2 text-muted"></i> Dificultad</span>
                            <span class="badge {{ $challenge->difficulty === 'easy' ? 'bg-success' : ($challenge->difficulty === 'medium' ? 'bg-warning' : 'bg-danger') }}">
                                {{ $challenge->difficulty === 'easy' ? 'Fácil' : ($challenge->difficulty === 'medium' ? 'Intermedio' : 'Avanzado') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-tasks me-2 text-muted"></i> Ejercicios</span>
                            <span>{{ $challenge->exercises->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-clock me-2 text-muted"></i> Tiempo Promedio</span>
                            <span>{{ $analytics->average_time_minutes ?? 0 }} minutos</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-check-circle me-2 text-muted"></i> Estado</span>
                            <span class="badge bg-{{ $challenge->status === 'draft' ? 'warning' : ($challenge->status === 'published' ? 'success' : 'danger') }}">
                                {{ $challenge->status === 'draft' ? 'Borrador' : ($challenge->status === 'published' ? 'Publicado' : 'Archivado') }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Rendimiento por ejercicio -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Rendimiento por Ejercicio</h5>
                </div>
                <div class="card-body">
                    @if($challenge->exercises->isEmpty())
                        <p class="text-muted text-center">No hay ejercicios para analizar.</p>
                    @else
                        @php
                            // Simular datos para el gráfico (en una app real, estos vendrían del modelo)
                            $exerciseStats = [];
                            foreach($challenge->exercises as $ex) {
                                $exerciseStats[] = [
                                    'title' => $ex->title,
                                    'completed' => rand(0, $analytics->total_students),
                                    'avgScore' => rand(50, 100)
                                ];
                            }
                        @endphp
                        <div class="list-group list-group-flush">
                            @foreach($exerciseStats as $index => $stat)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-bold small">Ejercicio {{ $index + 1 }}</span>
                                        <span class="text-muted small">
                                            {{ $stat['completed'] }}/{{ $analytics->total_students ?? 0 }} completados
                                        </span>
                                    </div>
                                    <div class="progress mb-1" style="height: 10px;">
                                        <div class="progress-bar {{ $stat['avgScore'] >= 70 ? 'bg-success' : ($stat['avgScore'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                             role="progressbar" style="width: {{ $stat['avgScore'] }}%" 
                                             aria-valuenow="{{ $stat['avgScore'] }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Puntuación promedio</span>
                                        <span class="small">{{ $stat['avgScore'] }}/100</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 