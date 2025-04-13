@extends('layouts.app')

@section('title', $leaderboard->name)

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('gamification.leaderboards.index') }}">Tabla de Clasificación</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $leaderboard->name }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h2 mb-0">{{ $leaderboard->name }}</h1>
            
            <div>
                <a href="{{ route('gamification.leaderboards.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-2"></i> Ver todas las clasificaciones
                </a>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ranking de Jugadores</h5>
                    @if($leaderboard->is_active)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Cerrado</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($entries->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                            <h3>No hay datos de clasificación disponibles</h3>
                            <p class="text-muted">Completa desafíos y ejercicios para aparecer en el ranking.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="7%">#</th>
                                        <th width="38%">Usuario</th>
                                        <th width="15%" class="text-center">Racha</th>
                                        <th width="15%" class="text-center">Desafíos</th>
                                        <th width="15%" class="text-center">Ejercicios</th>
                                        <th width="10%" class="text-end">Puntos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entries as $entry)
                                        <tr class="{{ $entry->user_id == auth()->id() ? 'table-primary' : '' }}">
                                            <td>
                                                @if($entry->ranking_position <= 3)
                                                    <span class="badge rounded-pill {{ $entry->ranking_position == 1 ? 'bg-warning' : ($entry->ranking_position == 2 ? 'bg-secondary' : 'bg-danger') }}">
                                                        {{ $entry->ranking_position }}
                                                    </span>
                                                @else
                                                    {{ $entry->ranking_position }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($entry->user->avatar_url)
                                                        <img src="{{ $entry->user->avatar_url }}" alt="{{ $entry->user->first_name }}" class="rounded-circle me-2" width="32" height="32">
                                                    @else
                                                        <div class="avatar-placeholder me-2">{{ strtoupper(substr($entry->user->first_name, 0, 1)) }}</div>
                                                    @endif
                                                    <span>{{ $entry->user->first_name }} {{ $entry->user->last_name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $entry->streak }} días</span>
                                            </td>
                                            <td class="text-center">{{ $entry->completed_challenges }}</td>
                                            <td class="text-center">{{ $entry->completed_exercises }}</td>
                                            <td class="text-end fw-bold">{{ $entry->score }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $entries->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @if(auth()->check() && $userPosition)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tu posición en el ranking</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="badge rounded-pill bg-primary p-3">{{ $userPosition['position'] }}</span>
                                </div>
                                <div>
                                    <h5 class="mb-0">Posición #{{ $userPosition['position'] }}</h5>
                                    <p class="text-muted mb-0">En {{ $leaderboard->name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-4">
                                    <div class="text-center">
                                        <h3 class="mb-0">{{ $userPosition['score'] }}</h3>
                                        <p class="text-muted mb-0">Puntos</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <h3 class="mb-0">{{ $userPosition['completed_challenges'] }}</h3>
                                        <p class="text-muted mb-0">Desafíos</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <h3 class="mb-0">{{ $userPosition['streak'] }}</h3>
                                        <p class="text-muted mb-0">Racha</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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