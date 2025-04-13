@extends('layouts.app')

@section('title', 'Entregables de Equipo - ' . $team->name)

@section('header', 'Entregables de Equipo - ' . $team->name)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.hackathons.index') }}">Hackathones</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.hackathons.details', ['id' => $team->hackathon->id]) }}">{{ $team->hackathon->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.hackathons.team', ['id' => $team->team_id]) }}">{{ $team->team_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Entregas de equipo</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Información del equipo y ronda actual -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ $team->name }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">{{ $team->hackathon->title }}</p>
                
                <h6 class="fw-bold mb-2">Miembros del equipo</h6>
                <ul class="list-group mb-3">
                    @foreach($team->members as $member)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ $member->profile_image ?? asset('img/avatars/default.png') }}" class="rounded-circle me-2" width="25" height="25" alt="Avatar">
                            <span>{{ $member->name }} {{ $member->id == Auth::id() ? '(Tú)' : '' }}</span>
                        </div>
                        @if($member->id == $team->leader_id)
                        <span class="badge rounded-pill bg-primary">Líder</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('student.hackathons.team.chat', ['id' => $team->id]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-comments me-1"></i> Chat de equipo
                    </a>
                    @if($team->repository_url)
                    <a href="{{ route('student.hackathons.team.repository', ['id' => $team->id]) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-code-branch me-1"></i> Repositorio
                    </a>
                    @else
                    <a href="{{ route('student.hackathons.team.edit', ['id' => $team->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-link me-1"></i> Añadir repositorio
                    </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Información de la fase actual -->
        @if($currentRound)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Fase Actual: {{ $currentRound->name ?? 'Ronda ' . $team->hackathon->current_round }}</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">Estado de la ronda</h6>
                    @php
                        $daysRemaining = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($currentRound->end_date), false);
                    @endphp
                    <span class="badge bg-{{ $daysRemaining <= 2 ? 'warning' : 'info' }}">
                        Tiempo restante: {{ $daysRemaining }} días
                    </span>
                </div>
                
                <p class="mb-2"><strong>Inicio:</strong> {{ \Carbon\Carbon::parse($currentRound->start_date)->format('d/m/Y') }}</p>
                <p class="mb-2"><strong>Finalización:</strong> {{ \Carbon\Carbon::parse($currentRound->end_date)->format('d/m/Y') }}</p>
                
                <div class="progress mt-3" style="height: 8px;">
                    @php
                        $totalRounds = 3; // Por defecto
                        $currentRoundNumber = $team->hackathon->current_round ?? 1;
                        $progress = ($currentRoundNumber / $totalRounds) * 100;
                    @endphp
                    <div class="progress-bar progress-bar-striped progress-bar-animated 
                        {{ $currentRoundNumber > 1 ? 'bg-success' : '' }}" 
                        role="progressbar" 
                        style="width: {{ $progress }}%" 
                        aria-valuenow="{{ $progress }}" 
                        aria-valuemin="0" 
                        aria-valuemax="100"></div>
                </div>
                <small class="text-muted">Ronda {{ $currentRoundNumber }} de {{ $totalRounds }}</small>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Objetivos de la Ronda</h6>
            </div>
            <div class="card-body">
                {!! $currentRound->objectives ?? '<p>No hay objetivos definidos para esta ronda.</p>' !!}
            </div>
        </div>
        @endif
    </div>
    
    <!-- Entregables y formulario de envío -->
    <div class="col-md-8">
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
        
        <!-- Formulario para subir entregable -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Subir Entregable</h5>
                <span class="badge rounded-pill bg-light text-dark">Ronda Actual</span>
            </div>
            <div class="card-body">
                @if($currentRound)
                <form action="{{ route('student.hackathons.team.deliverables.upload', ['id' => $team->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="round_id" value="{{ $currentRound->id }}">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Título del entregable <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Describe brevemente el contenido del entregable y cómo contribuye a los objetivos de la ronda.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file" class="form-label">Archivo <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Archivos permitidos: documentos, presentaciones, imágenes, archivos ZIP (máximo 20MB).</div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> Subir Entregable
                        </button>
                    </div>
                </form>
                @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i> No hay ninguna ronda activa en este momento. Los entregables estarán disponibles cuando comience la próxima ronda.
                </div>
                @endif
            </div>
        </div>
        
        <!-- Lista de entregables de la ronda actual -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Entregables de la Ronda Actual</h5>
            </div>
            <div class="card-body">
                @if($deliverables && $deliverables->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Fecha</th>
                                <th>Archivo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deliverables as $deliverable)
                            <tr>
                                <td>
                                    <strong>{{ $deliverable->title }}</strong>
                                    <p class="text-muted small mb-0">{{ Str::limit($deliverable->description, 50) }}</p>
                                </td>
                                <td>{{ $deliverable->user->name }}</td>
                                <td>{{ $deliverable->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('student.hackathons.deliverable.download', $deliverable->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Descargar
                                    </a>
                                </td>
                                <td>
                                    @if($deliverable->isEvaluated())
                                    <span class="badge bg-success">Evaluado ({{ $deliverable->score }}/10)</span>
                                    @else
                                    <span class="badge bg-warning">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-light">
                    <i class="fas fa-info-circle me-2"></i> No hay entregables para la ronda actual.
                </div>
                @endif
            </div>
        </div>
        
        <!-- Entregables de rondas anteriores -->
        @if($pastDeliverables && $pastDeliverables->count() > 0)
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Entregables de Rondas Anteriores</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="pastDeliverablesAccordion">
                    @php
                        $roundsWithDeliverables = $pastDeliverables->groupBy('round_id');
                    @endphp
                    
                    @foreach($roundsWithDeliverables as $roundId => $roundDeliverables)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $roundId }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $roundId }}" aria-expanded="false" aria-controls="collapse{{ $roundId }}">
                                Ronda {{ $roundDeliverables->first()->round->name ?? $roundId }} 
                                <span class="badge bg-primary ms-2">{{ $roundDeliverables->count() }} entregables</span>
                            </button>
                        </h2>
                        <div id="collapse{{ $roundId }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $roundId }}" data-bs-parent="#pastDeliverablesAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Autor</th>
                                                <th>Fecha</th>
                                                <th>Archivo</th>
                                                <th>Evaluación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roundDeliverables as $deliverable)
                                            <tr>
                                                <td>{{ $deliverable->title }}</td>
                                                <td>{{ $deliverable->user->name }}</td>
                                                <td>{{ $deliverable->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <a href="{{ route('student.hackathons.deliverable.download', $deliverable->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i> Descargar
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($deliverable->isEvaluated())
                                                    <span class="badge bg-success">{{ $deliverable->score }}/10</span>
                                                    @if($deliverable->feedback)
                                                    <button class="btn btn-sm btn-link p-0 ms-1" 
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="{{ $deliverable->feedback }}">
                                                        <i class="fas fa-comment-dots"></i>
                                                    </button>
                                                    @endif
                                                    @else
                                                    <span class="badge bg-secondary">Sin evaluar</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Validación del tamaño de archivo
        const fileInput = document.getElementById('file');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const maxSize = 20 * 1024 * 1024; // 20MB
                if (this.files[0] && this.files[0].size > maxSize) {
                    alert('El archivo seleccionado excede el tamaño máximo permitido (20MB).');
                    this.value = '';
                }
            });
        }
    });
</script>
@endsection 