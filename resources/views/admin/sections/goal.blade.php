<!-- Tab de Objetivo Educativo -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-3">Objetivo Educativo</h4>
        
        <div class="mb-3">
            <label for="goal_title" class="form-label">Título de la sección</label>
            <input type="text" class="form-control @error('goal_title') is-invalid @enderror" 
                id="goal_title" name="goal_title" value="{{ old('goal_title', $settings->goal_title) }}">
            @error('goal_title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="goal_subtitle" class="form-label">Subtítulo / Descripción</label>
            <textarea class="form-control @error('goal_subtitle') is-invalid @enderror" 
                id="goal_subtitle" name="goal_subtitle" rows="4">{{ old('goal_subtitle', $settings->goal_subtitle) }}</textarea>
            @error('goal_subtitle')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <h5 class="mb-3">Meta de Estudiantes</h5>
        
        <div class="mb-3">
            <label for="goal_students_target" class="form-label">Número objetivo de estudiantes</label>
            <input type="number" class="form-control @error('goal_students_target') is-invalid @enderror" 
                id="goal_students_target" name="goal_students_target" value="{{ old('goal_students_target', $settings->goal_students_target) }}">
            @error('goal_students_target')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="goal_year" class="form-label">Año objetivo</label>
            <input type="number" class="form-control @error('goal_year') is-invalid @enderror" 
                id="goal_year" name="goal_year" value="{{ old('goal_year', $settings->goal_year) }}">
            @error('goal_year')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="col-md-6">
        <h5 class="mb-3">Estadísticas Actuales</h5>
        
        <div class="mb-3">
            <label for="current_students" class="form-label">Estudiantes Actuales</label>
            <input type="number" class="form-control @error('current_students') is-invalid @enderror" 
                id="current_students" name="current_students" value="{{ old('current_students', $settings->current_students) }}">
            @error('current_students')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="current_projects" class="form-label">Proyectos Creados</label>
            <input type="number" class="form-control @error('current_projects') is-invalid @enderror" 
                id="current_projects" name="current_projects" value="{{ old('current_projects', $settings->current_projects) }}">
            @error('current_projects')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="current_badges" class="form-label">Insignias Ganadas</label>
            <input type="number" class="form-control @error('current_badges') is-invalid @enderror" 
                id="current_badges" name="current_badges" value="{{ old('current_badges', $settings->current_badges) }}">
            @error('current_badges')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div> 