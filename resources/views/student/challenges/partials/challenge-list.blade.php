@if($desafios->isEmpty())
    <div class="alert alert-info" role="alert">
        No hay desafíos en esta categoría.
    </div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($desafios as $desafio)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-{{ isset($progressMap[$desafio->id]) && $progressMap[$desafio->id]->status == 'completed' ? 'success' : (isset($progressMap[$desafio->id]) && $progressMap[$desafio->id]->status == 'in_progress' ? 'warning' : 'light') }} bg-opacity-25 d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ isset($progressMap[$desafio->id]) && $progressMap[$desafio->id]->status == 'completed' ? 'success' : (isset($progressMap[$desafio->id]) && $progressMap[$desafio->id]->status == 'in_progress' ? 'warning' : 'light text-dark') }}">
                            {{ isset($progressMap[$desafio->id]) && $progressMap[$desafio->id]->status == 'completed' ? 'Completado' : (isset($progressMap[$desafio->id]) && $progressMap[$desafio->id]->status == 'in_progress' ? 'En Progreso' : 'No Iniciado') }}
                        </span>
                        <span class="badge bg-secondary">
                            {{ $desafio->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $desafio->title }}</h5>
                        <p class="card-text text-muted small">
                            {{ Str::limit($desafio->description, 80) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center small mb-2">
                            <span class="text-muted">
                                <i class="fas fa-graduation-cap"></i> {{ $desafio->classroom ? $desafio->classroom->class_name : 'Público' }}
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-trophy"></i> {{ $desafio->points }} pts
                            </span>
                        </div>
                        
                        @if(isset($progressMap[$desafio->id]))
                            <div class="progress mb-1" style="height: 4px;">
                                <div class="progress-bar {{ $progressMap[$desafio->id]->status === 'completed' ? 'bg-success' : 'bg-info' }}" 
                                    style="width: {{ isset($progressMap[$desafio->id]) && method_exists($progressMap[$desafio->id], 'getCompletionPercentageAttribute') ? $progressMap[$desafio->id]->getCompletionPercentageAttribute() : 0 }}%"
                                    role="progressbar"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-muted">
                                    <i class="fas fa-tasks"></i> {{ $progressMap[$desafio->id]->completed_exercises ?? 0 }}/{{ $progressMap[$desafio->id]->total_exercises ?? 1 }} ejercicios
                                </span>
                                <span class="badge bg-{{ $desafio->difficulty == 'principiante' ? 'success' : ($desafio->difficulty == 'intermedio' ? 'primary' : 'danger') }}">
                                    {{ ucfirst($desafio->difficulty) }}
                                </span>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-muted">
                                    <i class="fas fa-tasks"></i> {{ $desafio->exercises->count() }} ejercicios
                                </span>
                                <span class="badge bg-{{ $desafio->difficulty == 'principiante' ? 'success' : ($desafio->difficulty == 'intermedio' ? 'primary' : 'danger') }}">
                                    {{ ucfirst($desafio->difficulty) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('student.challenges.show', $desafio->id) }}" class="btn btn-primary w-100">
                            @if(isset($progressMap[$desafio->id]) && $progressMap[$desafio->id]->status == 'completed')
                                <i class="fas fa-check"></i> Ver Completado
                            @elseif(isset($progressMap[$desafio->id]) && $progressMap[$desafio->id]->status == 'in_progress')
                                <i class="fas fa-play"></i> Continuar
                            @else
                                <i class="fas fa-play"></i> Empezar
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif 