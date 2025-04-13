@extends('layouts.app')

@section('title', 'Detalles de Clase')

@section('header', 'Detalles de Clase')

@section('content')
    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $classroom->name }}</h5>
                    <span class="badge bg-light text-primary">
                        {{ $classroom->is_active ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Descripción</h6>
                            <p>{{ $classroom->description ?: 'Sin descripción' }}</p>
                            
                            <h6 class="fw-bold">Código de acceso</h6>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ $classroom->access_code }}" readonly id="access_code_input">
                                <button class="btn btn-outline-secondary" type="button" id="copy_code_btn">Copiar</button>
                            </div>
                            
                            <h6 class="fw-bold">Fecha de creación</h6>
                            <p>{{ $classroom->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="fw-bold">Estudiantes inscritos ({{ $classroom->enrollments->count() }})</h6>
                            @if($classroom->enrollments->count() > 0)
                                <ul class="list-group">
                                    @foreach($classroom->students as $student)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                            <span class="badge bg-primary rounded-pill">
                                                {{ $student->studentProfile->level ?? '1' }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No hay estudiantes inscritos en esta clase.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver a Mis Clases
                        </a>
                        
                        <div>
                            <a href="{{ route('classrooms.edit', $classroom->class_id) }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash-alt me-1"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Proyectos de la Clase</h5>
                </div>
                <div class="card-body">
                    @if($classroom->projects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Proyecto</th>
                                        <th>Estudiante</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classroom->projects as $project)
                                        <tr>
                                            <td>{{ $project->title }}</td>
                                            <td>{{ $project->user->first_name }} {{ $project->user->last_name }}</td>
                                            <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $project->is_completed ? 'success' : 'warning' }}">
                                                    {{ $project->is_completed ? 'Completado' : 'En progreso' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $project->project_id) }}" class="btn btn-sm btn-outline-primary">
                                                    Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay proyectos asociados a esta clase.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmación para Eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta clase? Esta acción no se puede deshacer.</p>
                    <p class="text-danger fw-bold">Advertencia: Se eliminarán todas las inscripciones de estudiantes y los datos asociados a esta clase.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('classrooms.destroy', $classroom->class_id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para copiar el código de acceso
            const copyCodeBtn = document.getElementById('copy_code_btn');
            const accessCodeInput = document.getElementById('access_code_input');
            
            copyCodeBtn.addEventListener('click', function() {
                accessCodeInput.select();
                document.execCommand('copy');
                
                // Cambiar el texto del botón temporalmente
                const originalText = copyCodeBtn.textContent;
                copyCodeBtn.textContent = '¡Copiado!';
                setTimeout(() => {
                    copyCodeBtn.textContent = originalText;
                }, 2000);
            });
        });
    </script>
    @endpush
@endsection 