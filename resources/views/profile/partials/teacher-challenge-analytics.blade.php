<section>
    <header>
        <h2 class="h5 mb-3">
            Analíticas de tus Desafíos
        </h2>
        <p class="text-muted">
            Visualiza el rendimiento de tus desafíos de enseñanza y el progreso de tus estudiantes.
        </p>
    </header>

    @if($challengeAnalytics && $challengeAnalytics->count() > 0)
        <!-- Tarjetas de resumen -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="fas fa-book fa-2x text-primary"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">{{ $challengeAnalytics->count() }}</h5>
                        <p class="text-muted mb-0">Desafíos Creados</p>
                    </div>
                </div>
            </div>
            
            @php
                $totalStudents = $challengeAnalytics->sum(function($challenge) {
                    return $challenge->analytics ? $challenge->analytics->total_students : 0;
                });
                
                $totalStarted = $challengeAnalytics->sum(function($challenge) {
                    return $challenge->analytics ? $challenge->analytics->started_count : 0;
                });
                
                $totalCompleted = $challengeAnalytics->sum(function($challenge) {
                    return $challenge->analytics ? $challenge->analytics->completed_count : 0;
                });
                
                $avgCompletionRate = $totalStarted > 0 ? round(($totalCompleted / $totalStarted) * 100) : 0;
            @endphp
            
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="fas fa-users fa-2x text-success"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">{{ $totalStudents }}</h5>
                        <p class="text-muted mb-0">Estudiantes Totales</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                <i class="fas fa-tasks fa-2x text-info"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">{{ $totalStarted }}</h5>
                        <p class="text-muted mb-0">Desafíos Iniciados</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="fas fa-chart-line fa-2x text-warning"></i>
                            </div>
                        </div>
                        <h5 class="mb-0">{{ $avgCompletionRate }}%</h5>
                        <p class="text-muted mb-0">Tasa de Finalización</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header py-3 bg-white">
                <h5 class="mb-0">Resumen de Desafíos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Desafío</th>
                                <th>Total Estudiantes</th>
                                <th>Iniciados</th>
                                <th>Completados</th>
                                <th>% Completado</th>
                                <th>Nota Promedio</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($challengeAnalytics as $challenge)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge {{ $challenge->status === 'published' ? 'bg-success' : ($challenge->status === 'draft' ? 'bg-warning' : 'bg-secondary') }} me-2">
                                                {{ $challenge->status === 'published' ? 'Publicado' : ($challenge->status === 'draft' ? 'Borrador' : 'Archivado') }}
                                            </span>
                                            <strong>{{ Str::limit($challenge->title, 30) }}</strong>
                                        </div>
                                        <small class="text-muted">{{ $challenge->challenge_type === 'python' ? 'Python' : 'Prompting IA' }}</small>
                                    </td>
                                    <td>
                                        @if($challenge->analytics)
                                            {{ $challenge->analytics->total_students ?? 0 }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                        @if($challenge->analytics)
                                            {{ $challenge->analytics->started_count ?? 0 }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                        @if($challenge->analytics)
                                            {{ $challenge->analytics->completed_count ?? 0 }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                        @if($challenge->analytics && $challenge->analytics->started_count > 0)
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ ($challenge->analytics->completed_count / $challenge->analytics->started_count) * 100 }}%"></div>
                                                </div>
                                                <span>{{ round(($challenge->analytics->completed_count / $challenge->analytics->started_count) * 100) }}%</span>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                                </div>
                                                <span>0%</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($challenge->analytics && $challenge->analytics->average_score > 0)
                                            {{ number_format($challenge->analytics->average_score, 1) }}/10
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('challenges.analytics', $challenge->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-chart-bar me-1"></i> Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <a href="{{ route('challenges.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-graduation-cap me-1"></i> Ver todos mis desafíos
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <p class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Aún no has creado ningún desafío de enseñanza. ¡Comienza a crear contenido para tus estudiantes!
            </p>
            <div class="mt-3">
                <a href="{{ route('challenges.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Crear nuevo desafío
                </a>
            </div>
        </div>
    @endif
</section>
 