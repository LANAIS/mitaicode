<div class="row">
    <div class="col-md-8">
        <!-- Card de información del equipo -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Equipo: {{ $team->team_name }}</h5>
                <span class="badge bg-primary">{{ $team->members->count() }} miembros</span>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">{{ $hackathon->title }}</p>
                
                <!-- Acciones del equipo -->
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <a href="{{ route('student.hackathons.team.chat', $team->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-comments me-1"></i> Chat de equipo
                    </a>
                    
                    <a href="{{ route('student.hackathons.team.deliverables', $team->id) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-upload me-1"></i> Entregables
                    </a>
                    
                    @if($isLeader)
                    <a href="{{ route('student.hackathons.team.edit', $team->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> Editar equipo
                    </a>
                    @endif
                    
                    @if($team->repository_url)
                    <a href="{{ $team->repository_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-code-branch me-1"></i> Repositorio
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 