@if($challenges->isEmpty())
    <div class="alert alert-info" role="alert">
        No hay desafíos en esta categoría.
    </div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($challenges as $challenge)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-{{ $challenge->status == 'published' ? 'success' : ($challenge->status == 'draft' ? 'warning' : 'danger') }} bg-opacity-25 d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $challenge->status == 'published' ? 'success' : ($challenge->status == 'draft' ? 'warning' : 'danger') }}">
                            {{ $challenge->status == 'published' ? 'Publicado' : ($challenge->status == 'draft' ? 'Borrador' : 'Archivado') }}
                        </span>
                        <span class="badge bg-secondary">
                            {{ $challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $challenge->title }}</h5>
                        <p class="card-text text-muted small">
                            {{ Str::limit($challenge->description, 80) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center small mb-2">
                            <span class="text-muted">
                                <i class="fas fa-graduation-cap"></i> {{ $challenge->classroom ? $challenge->classroom->class_name : 'Público' }}
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-trophy"></i> {{ $challenge->points }} pts
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">
                                <i class="fas fa-tasks"></i> {{ $challenge->exercises->count() }} ejercicios
                            </span>
                            <span class="badge bg-{{ $challenge->difficulty == 'principiante' ? 'success' : ($challenge->difficulty == 'intermedio' ? 'primary' : 'danger') }}">
                                {{ ucfirst($challenge->difficulty) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent d-flex gap-1">
                        <a href="{{ route('challenges.show', $challenge->id) }}" class="btn btn-sm btn-primary flex-grow-1">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('challenges.edit', $challenge->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('challenges.preview', $challenge->id) }}" class="btn btn-sm btn-outline-secondary" title="Vista previa estudiante">
                            <i class="fas fa-user-graduate"></i>
                        </a>
                        @if($challenge->status != 'archived')
                            <a href="{{ route('challenges.analytics', $challenge->id) }}" class="btn btn-sm btn-outline-info" title="Analíticas">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif 