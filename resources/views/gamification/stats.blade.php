@extends('layouts.app')

@section('title', 'Mis Estadísticas')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">Mis Estadísticas</h1>
            <p class="text-muted">Visualiza tu progreso y compite con otros estudiantes</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i> Volver al Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Mi Progreso</h5>
                </div>
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center my-3">
                        <div class="text-center px-3">
                            <h5 class="mb-0">{{ $stats['streak'] }}</h5>
                            <small class="text-muted">Racha Actual</small>
                        </div>
                        <div class="text-center px-3 border-start">
                            <h5 class="mb-0">{{ $stats['longest_streak'] }}</h5>
                            <small class="text-muted">Racha Máxima</small>
                        </div>
                        <div class="text-center px-3 border-start">
                            <h5 class="mb-0">{{ $stats['all_time_score'] }}</h5>
                            <small class="text-muted">Puntos Totales</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Desafíos y Ejercicios</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Desafíos Completados</span>
                        <span class="badge bg-primary rounded-pill">{{ $stats['completed_challenges'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Ejercicios Completados</span>
                        <span class="badge bg-primary rounded-pill">{{ $stats['completed_exercises'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Logros Desbloqueados</span>
                        <span class="badge bg-primary rounded-pill">{{ $stats['achievements_count'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Mis Rankings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Semanal</h5>
                                    @if(isset($stats['weekly_position']))
                                        <h3 class="display-4 fw-bold">{{ $stats['weekly_position'] }}</h3>
                                        <p class="text-muted">Posición</p>
                                        <p>{{ $stats['weekly_score'] ?? 0 }} puntos</p>
                                    @else
                                        <p class="text-muted mt-4">Sin posición</p>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="{{ route('gamification.leaderboards.index', ['type' => 'weekly']) }}" class="btn btn-sm btn-outline-primary">Ver Tabla</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Mensual</h5>
                                    @if(isset($stats['monthly_position']))
                                        <h3 class="display-4 fw-bold">{{ $stats['monthly_position'] }}</h3>
                                        <p class="text-muted">Posición</p>
                                        <p>{{ $stats['monthly_score'] ?? 0 }} puntos</p>
                                    @else
                                        <p class="text-muted mt-4">Sin posición</p>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="{{ route('gamification.leaderboards.index', ['type' => 'monthly']) }}" class="btn btn-sm btn-outline-primary">Ver Tabla</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card text-center h-100">
                                <div class="card-body">
                                    <h5 class="card-title">General</h5>
                                    @if(isset($stats['all_time_position']))
                                        <h3 class="display-4 fw-bold">{{ $stats['all_time_position'] }}</h3>
                                        <p class="text-muted">Posición</p>
                                        <p>{{ $stats['all_time_score'] }} puntos</p>
                                    @else
                                        <p class="text-muted mt-4">Sin posición</p>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="{{ route('gamification.leaderboards.index', ['type' => 'all_time']) }}" class="btn btn-sm btn-outline-primary">Ver Tabla</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Logros Recientes</h5>
                    <a href="{{ route('gamification.achievements.index') }}" class="btn btn-sm btn-light">Ver Todos</a>
                </div>
                <div class="card-body">
                    @if($recentAchievements->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                            <h5>No tienes logros todavía</h5>
                            <p class="text-muted">Completa desafíos y ejercicios para desbloquear logros</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentAchievements as $achievement)
                                <div class="list-group-item border-0 d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        @if($achievement->icon)
                                            <i class="fas fa-{{ $achievement->icon }} fa-2x text-primary"></i>
                                        @else
                                            <i class="fas fa-award fa-2x text-primary"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $achievement->description }}</h6>
                                        <small class="text-muted">
                                            Desbloqueado el {{ $achievement->awarded_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <span class="badge bg-success">+{{ $achievement->points }} pts</span>
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