<div class="d-flex gap-2 mb-4">
    <a href="{{ route('hackathons.edit', $hackathon->id) }}" class="btn btn-primary">
        <i class="fas fa-edit me-1"></i> Editar Hackathon
    </a>
    
    <a href="{{ route('hackathons.rounds.index', $hackathon->id) }}" class="btn btn-success">
        <i class="fas fa-tasks me-1"></i> Gestionar Rondas
    </a>
    
    <a href="{{ route('hackathons.deliverables.evaluate', $hackathon->id) }}" class="btn btn-info">
        <i class="fas fa-clipboard-check me-1"></i> Evaluar Entregables
    </a>
    
    <!-- ... existing buttons ... -->
</div> 