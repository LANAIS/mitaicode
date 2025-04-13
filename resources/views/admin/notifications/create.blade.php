@extends('layouts.admin')

@section('styles')
<style>
    .form-control {
        background-color: #fff;
        color: #333;
        border: 1px solid #d1d3e2;
    }
    
    .form-control:focus {
        background-color: #fff;
        color: #333;
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .form-label {
        color: #333;
        font-weight: 600;
    }
    
    .form-check-label {
        color: #333;
    }
    
    .card {
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        color: #333;
    }
    
    .input-group-text {
        background-color: #f8f9fc;
        border: 1px solid #d1d3e2;
        color: #6e707e;
    }
    
    .text-danger {
        color: #e74a3b !important;
    }
    
    .text-dark {
        color: #333 !important;
    }
    
    .btn-secondary {
        color: #fff;
        background-color: #858796;
        border-color: #858796;
    }
    
    .btn-primary {
        color: #fff;
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    small.text-muted {
        color: #666 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3 pt-2">Nueva Notificación</h6>
                        <div class="pe-3">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm bg-white text-dark">
                                <i class="fas fa-arrow-left text-sm"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pb-2">
                    @if($errors->any())
                    <div class="alert alert-danger text-white">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Título de la Notificación</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                                    <small class="text-muted">Nombre interno para identificar esta notificación</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="type" class="form-label">Tipo</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="welcome" {{ old('type') == 'welcome' ? 'selected' : '' }}>Bienvenida</option>
                                        <option value="reminder" {{ old('type') == 'reminder' ? 'selected' : '' }}>Recordatorio</option>
                                        <option value="inactive" {{ old('type') == 'inactive' ? 'selected' : '' }}>Reactivación</option>
                                        <option value="new_content" {{ old('type') == 'new_content' ? 'selected' : '' }}>Nuevo contenido</option>
                                        <option value="marketing" {{ old('type') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Asunto del Email</label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="audience" class="form-label">Audiencia</label>
                                    <select class="form-control" id="audience" name="audience" required>
                                        <option value="">Seleccionar audiencia</option>
                                        <option value="all" {{ old('audience') == 'all' ? 'selected' : '' }}>Todos los usuarios</option>
                                        <option value="students" {{ old('audience') == 'students' ? 'selected' : '' }}>Solo estudiantes</option>
                                        <option value="teachers" {{ old('audience') == 'teachers' ? 'selected' : '' }}>Solo profesores</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="expires_at" class="form-label">Fecha de Expiración (opcional)</label>
                                    <input type="date" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                    <small class="text-muted">Dejar en blanco si la notificación no expira</small>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="active" name="active" {{ old('active') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">Notificación activa</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_once" name="show_once" {{ old('show_once') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_once">Mostrar solo una vez por usuario</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="message" class="form-label">Contenido del Email</label>
                                    <textarea class="form-control" id="message" name="message" rows="12" required>{{ old('message') }}</textarea>
                                    <small class="text-muted">Puedes usar etiquetas HTML y variables como {{name}}, {{email}}, etc.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear Notificación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar los componentes de Material Dashboard aquí si es necesario
    });
</script>
@endsection 