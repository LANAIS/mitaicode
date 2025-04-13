@extends('layouts.app')

@section('title', 'Desafíos Disponibles')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h1 class="h2">Desafíos</h1>
        <p class="text-muted">Practica tus habilidades con estos desafíos</p>
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

    @if($allDesafios->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center p-5">
                <i class="fas fa-tasks text-muted mb-3" style="font-size: 3rem;"></i>
                <h4>No hay desafíos disponibles</h4>
                <p class="text-muted">Aún no hay desafíos disponibles para ti. Consulta con tu profesor para más información.</p>
            </div>
        </div>
    @else
        <div class="row mb-4">
            <div class="col">
                <ul class="nav nav-tabs" id="challengesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                            Todos <span class="badge bg-secondary ms-1">{{ $allDesafios->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="python-tab" data-bs-toggle="tab" data-bs-target="#python" type="button" role="tab" aria-controls="python" aria-selected="false">
                            Python <span class="badge bg-secondary ms-1">{{ $allDesafios->where('challenge_type', 'python')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ai-prompt-tab" data-bs-toggle="tab" data-bs-target="#ai-prompt" type="button" role="tab" aria-controls="ai-prompt" aria-selected="false">
                            Prompts IA <span class="badge bg-secondary ms-1">{{ $allDesafios->where('challenge_type', 'ai_prompt')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
                            Completados <span class="badge bg-success ms-1">{{ $allDesafios->filter(function($d) use ($progressMap) { return isset($progressMap[$d->id]) && $progressMap[$d->id]->status === 'completed'; })->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="in-progress-tab" data-bs-toggle="tab" data-bs-target="#in-progress" type="button" role="tab" aria-controls="in-progress" aria-selected="false">
                            En Progreso <span class="badge bg-warning ms-1">{{ $allDesafios->filter(function($d) use ($progressMap) { return isset($progressMap[$d->id]) && $progressMap[$d->id]->status === 'in_progress'; })->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="challengesTabsContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                @include('student.challenges.partials.challenge-list', ['desafios' => $allDesafios, 'progressMap' => $progressMap])
            </div>
            <div class="tab-pane fade" id="python" role="tabpanel" aria-labelledby="python-tab">
                @include('student.challenges.partials.challenge-list', ['desafios' => $allDesafios->where('challenge_type', 'python'), 'progressMap' => $progressMap])
            </div>
            <div class="tab-pane fade" id="ai-prompt" role="tabpanel" aria-labelledby="ai-prompt-tab">
                @include('student.challenges.partials.challenge-list', ['desafios' => $allDesafios->where('challenge_type', 'ai_prompt'), 'progressMap' => $progressMap])
            </div>
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                @include('student.challenges.partials.challenge-list', ['desafios' => $allDesafios->filter(function($d) use ($progressMap) { return isset($progressMap[$d->id]) && $progressMap[$d->id]->status === 'completed'; }), 'progressMap' => $progressMap])
            </div>
            <div class="tab-pane fade" id="in-progress" role="tabpanel" aria-labelledby="in-progress-tab">
                @include('student.challenges.partials.challenge-list', ['desafios' => $allDesafios->filter(function($d) use ($progressMap) { return isset($progressMap[$d->id]) && $progressMap[$d->id]->status === 'in_progress'; }), 'progressMap' => $progressMap])
            </div>
        </div>
    @endif
</div>
@endsection 