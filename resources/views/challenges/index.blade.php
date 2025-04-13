@extends('layouts.app')

@section('title', 'Desafíos de Enseñanza')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Mis Desafíos</h1>
        <a href="{{ route('challenges.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Crear Desafío
        </a>
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

    @if($challenges->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center p-5">
                <i class="fas fa-tasks text-muted mb-3" style="font-size: 3rem;"></i>
                <h4>No hay desafíos todavía</h4>
                <p class="text-muted">Comienza creando tu primer desafío de enseñanza para tus estudiantes.</p>
                <a href="{{ route('challenges.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Desafío
                </a>
            </div>
        </div>
    @else
        <div class="row mb-4">
            <div class="col">
                <ul class="nav nav-tabs" id="challengesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                            Todos <span class="badge bg-secondary ms-1">{{ $challenges->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="published-tab" data-bs-toggle="tab" data-bs-target="#published" type="button" role="tab" aria-controls="published" aria-selected="false">
                            Publicados <span class="badge bg-success ms-1">{{ $challenges->where('status', 'published')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="draft-tab" data-bs-toggle="tab" data-bs-target="#draft" type="button" role="tab" aria-controls="draft" aria-selected="false">
                            Borradores <span class="badge bg-warning ms-1">{{ $challenges->where('status', 'draft')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archived" type="button" role="tab" aria-controls="archived" aria-selected="false">
                            Archivados <span class="badge bg-danger ms-1">{{ $challenges->where('status', 'archived')->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="challengesTabsContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                @include('challenges.partials.challenge-list', ['challenges' => $challenges])
            </div>
            <div class="tab-pane fade" id="published" role="tabpanel" aria-labelledby="published-tab">
                @include('challenges.partials.challenge-list', ['challenges' => $challenges->where('status', 'published')])
            </div>
            <div class="tab-pane fade" id="draft" role="tabpanel" aria-labelledby="draft-tab">
                @include('challenges.partials.challenge-list', ['challenges' => $challenges->where('status', 'draft')])
            </div>
            <div class="tab-pane fade" id="archived" role="tabpanel" aria-labelledby="archived-tab">
                @include('challenges.partials.challenge-list', ['challenges' => $challenges->where('status', 'archived')])
            </div>
        </div>
    @endif
</div>
@endsection 