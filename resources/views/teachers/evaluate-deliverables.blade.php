@extends('layouts.app')

@section('title', 'Evaluar Entregables - ' . $hackathon->title)

@section('header', 'Evaluar Entregables - ' . $hackathon->title)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hackathons.index') }}">Hackathones</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hackathons.show', ['id' => $hackathon->id]) }}">{{ $hackathon->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Evaluar Entregables</li>
        </ol>
    </nav>
</div>

<!-- Mensajes de alerta -->
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

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Información del Hackathon</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-4 fw-bold">Título:</div>
                    <div class="col-lg-8">{{ $hackathon->title }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-4 fw-bold">Descripción:</div>
                    <div class="col-lg-8">{{ Str::limit($hackathon->description, 150) }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-4 fw-bold">Ronda Actual:</div>
                    <div class="col-lg-8">{{ $hackathon->current_round }} de {{ $rounds->count() }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-4 fw-bold">Equipos:</div>
                    <div class="col-lg-8">{{ $teams->count() }}</div>
                </div>
                <div class="row">
                    <div class="col-lg-4 fw-bold">Estado:</div>
                    <div class="col-lg-8">
                        @if($hackathon->isActive())
                        <span class="badge bg-success">Activo</span>
                        @elseif($hackathon->hasEnded())
                        <span class="badge bg-secondary">Finalizado</span>
                        @else
                        <span class="badge bg-warning">Próximamente</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Seleccionar Ronda</h5>
            </div>
            <div class="card-body">
                <div class="list-group mb-3">
                    @foreach($rounds as $round)
                    <a href="{{ route('hackathons.deliverables.evaluate.round', ['hackathonId' => $hackathon->id, 'roundId' => $round->round_id]) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center 
                              {{ $roundId == $round->round_id ? 'active' : '' }}">
                        <div>
                            <h6 class="mb-1">{{ $round->title ?? 'Ronda ' . $round->round_number }}</h6>
                            <small>{{ \Carbon\Carbon::parse($round->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($round->end_date)->format('d/m/Y') }}</small>
                        </div>
                        <span class="badge rounded-pill 
                              {{ $round->hasEnded() ? 'bg-secondary' : ($round->isActive() ? 'bg-success' : 'bg-warning') }}">
                            {{ $round->hasEnded() ? 'Finalizada' : ($round->isActive() ? 'Activa' : 'Pendiente') }}
                        </span>
                    </a>
                    @endforeach
                </div>
                
                <!-- Estadísticas básicas de la ronda seleccionada -->
                @if($roundId)
                <div class="card bg-light">
                    <div class="card-body">
                        @php
                            $selectedRound = $rounds->firstWhere('round_id', $roundId);
                            $totalDeliverables = $teams->reduce(function ($carry, $team) {
                                return $carry + $team->deliverables->count();
                            }, 0);
                            $evaluatedDeliverables = $teams->reduce(function ($carry, $team) {
                                return $carry + $team->deliverables->filter(function($deliverable) {
                                    return $deliverable->isEvaluated();
                                })->count();
                            }, 0);
                            $progressPercentage = $totalDeliverables > 0 ? ($evaluatedDeliverables / $totalDeliverables) * 100 : 100;
                        @endphp
                        
                        <h6 class="card-title">Progreso de evaluación</h6>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage }}%;" 
                                 aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ round($progressPercentage) }}%
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <small>Evaluados: {{ $evaluatedDeliverables }} de {{ $totalDeliverables }}</small>
                            <small>Pendientes: {{ $totalDeliverables - $evaluatedDeliverables }}</small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Lista de equipos y entregables -->
@if($roundId)
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Entregables por Equipo</h5>
    </div>
    <div class="card-body">
        @if($teams->count() > 0)
        <div class="accordion" id="teamsAccordion">
            @foreach($teams as $team)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $team->team_id }}">
                    <button class="accordion-button {{ $team->deliverables->count() > 0 ? '' : 'collapsed' }}" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $team->team_id }}" 
                            aria-expanded="{{ $team->deliverables->count() > 0 ? 'true' : 'false' }}" 
                            aria-controls="collapse{{ $team->team_id }}">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                            <span>
                                <strong>{{ $team->team_name }}</strong>
                                <span class="badge rounded-pill bg-primary ms-2">{{ $team->members->count() }} miembros</span>
                            </span>
                            <span>
                                @if($team->deliverables->count() > 0)
                                <span class="badge rounded-pill bg-success">{{ $team->deliverables->count() }} entregables</span>
                                @else
                                <span class="badge rounded-pill bg-warning">Sin entregables</span>
                                @endif
                            </span>
                        </div>
                    </button>
                </h2>
                <div id="collapse{{ $team->team_id }}" class="accordion-collapse collapse {{ $team->deliverables->count() > 0 ? 'show' : '' }}" 
                     aria-labelledby="heading{{ $team->team_id }}" data-bs-parent="#teamsAccordion">
                    <div class="accordion-body">
                        <!-- Información del equipo -->
                        <div class="mb-4">
                            <h6 class="mb-2">Miembros del equipo:</h6>
                            <div class="list-group mb-3">
                                @foreach($team->members as $member)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $member->profile_image ?? asset('img/avatars/default.png') }}" class="rounded-circle me-2" width="30" height="30" alt="Avatar">
                                        <span>{{ $member->first_name }} {{ $member->last_name }} {{ $member->email }}</span>
                                    </div>
                                    @if($member->user_id == $team->leader_id)
                                    <span class="badge rounded-pill bg-primary">Líder</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Lista de entregables -->
                        @if($team->deliverables->count() > 0)
                        <h6 class="mb-2">Entregables:</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="35%">Título / Descripción</th>
                                        <th width="15%">Autor</th>
                                        <th width="15%">Fecha</th>
                                        <th width="15%">Archivo</th>
                                        <th width="20%">Evaluación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($team->deliverables as $deliverable)
                                    <tr>
                                        <td>
                                            <h6 class="mb-1">{{ $deliverable->title }}</h6>
                                            <small class="text-muted">{{ Str::limit($deliverable->description, 100) }}</small>
                                        </td>
                                        <td>{{ $deliverable->user->first_name }} {{ $deliverable->user->last_name }}</td>
                                        <td>{{ $deliverable->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('hackathons.deliverable.download', ['deliverableId' => $deliverable->id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i> Descargar
                                            </a>
                                        </td>
                                        <td>
                                            @if($deliverable->isEvaluated())
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">{{ $deliverable->score }}/10</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEvaluationModal{{ $deliverable->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-check-circle me-1"></i> Evaluado {{ $deliverable->evaluated_at->diffForHumans() }}
                                            </small>
                                            @else
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#evaluationModal{{ $deliverable->id }}">
                                                <i class="fas fa-star me-1"></i> Evaluar
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-light">
                            <i class="fas fa-info-circle me-2"></i> Este equipo no ha subido entregables para esta ronda.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> No hay equipos registrados en este hackathon.
        </div>
        @endif
    </div>
</div>

<!-- Modales para evaluación -->
@foreach($teams as $team)
    @foreach($team->deliverables as $deliverable)
        <!-- Modal para nuevas evaluaciones -->
        @if(!$deliverable->isEvaluated())
        <div class="modal fade" id="evaluationModal{{ $deliverable->id }}" tabindex="-1" aria-labelledby="evaluationModalLabel{{ $deliverable->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="evaluationModalLabel{{ $deliverable->id }}">Evaluar Entregable</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('hackathons.deliverable.evaluate', ['deliverableId' => $deliverable->id]) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Equipo</label>
                                <p>{{ $team->team_name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Entregable</label>
                                <p>{{ $deliverable->title }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Autor</label>
                                <p>{{ $deliverable->user->first_name }} {{ $deliverable->user->last_name }}</p>
                            </div>
                            <div class="mb-3">
                                <label for="score{{ $deliverable->id }}" class="form-label">Calificación (0-10)</label>
                                <input type="number" class="form-control" id="score{{ $deliverable->id }}" name="score" min="0" max="10" step="0.5" required>
                                <div class="form-text">Asigna una calificación entre 0 y 10 puntos.</div>
                            </div>
                            <div class="mb-3">
                                <label for="feedback{{ $deliverable->id }}" class="form-label">Retroalimentación</label>
                                <textarea class="form-control" id="feedback{{ $deliverable->id }}" name="feedback" rows="3"></textarea>
                                <div class="form-text">Proporciona comentarios constructivos sobre el entregable.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Evaluación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @else
        <!-- Modal para editar evaluaciones existentes -->
        <div class="modal fade" id="editEvaluationModal{{ $deliverable->id }}" tabindex="-1" aria-labelledby="editEvaluationModalLabel{{ $deliverable->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEvaluationModalLabel{{ $deliverable->id }}">Editar Evaluación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('hackathons.deliverable.evaluate', ['deliverableId' => $deliverable->id]) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Equipo</label>
                                <p>{{ $team->team_name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Entregable</label>
                                <p>{{ $deliverable->title }}</p>
                            </div>
                            <div class="mb-3">
                                <label for="scoreEdit{{ $deliverable->id }}" class="form-label">Calificación (0-10)</label>
                                <input type="number" class="form-control" id="scoreEdit{{ $deliverable->id }}" name="score" min="0" max="10" step="0.5" value="{{ $deliverable->score }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="feedbackEdit{{ $deliverable->id }}" class="form-label">Retroalimentación</label>
                                <textarea class="form-control" id="feedbackEdit{{ $deliverable->id }}" name="feedback" rows="3">{{ $deliverable->feedback }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Actualizar Evaluación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endforeach
@else
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i> Selecciona una ronda para ver los entregables correspondientes.
</div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Validación de calificaciones
        const scoreInputs = document.querySelectorAll('input[type="number"][name="score"]');
        scoreInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                const value = parseFloat(this.value);
                if (value < 0) this.value = 0;
                if (value > 10) this.value = 10;
            });
        });
    });
</script>
@endsection 