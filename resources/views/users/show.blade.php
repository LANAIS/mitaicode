@extends('layouts.app')

@section('title', 'Perfil de Usuario')

@section('header', 'Perfil de Usuario')

@section('content')
    <div class="row">
        <!-- Información del usuario -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3 position-relative">
                        @if($user->avatar_url)
                            <img src="{{ asset('storage/' . $user->avatar_url) }}" 
                                alt="{{ $user->first_name }} {{ $user->last_name }}" 
                                class="rounded-circle img-fluid mx-auto d-block" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . '+' . $user->last_name) }}&background=3490dc&color=ffffff&size=128" 
                                alt="{{ $user->first_name }} {{ $user->last_name }}" 
                                class="rounded-circle img-fluid mx-auto d-block" style="width: 120px; height: 120px;">
                        @endif
                        
                        @if($isOwnProfile)
                            <a href="{{ route('profile.edit') }}#info" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2" 
                               style="transform: translate(20%, 20%);" title="Cambiar avatar">
                                <i class="fas fa-camera"></i>
                            </a>
                        @endif
                    </div>
                    
                    <h4 class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    
                    @if($user->role === 'student')
                        <span class="badge bg-primary">Estudiante</span>
                    @elseif($user->role === 'teacher')
                        <span class="badge bg-success">Profesor</span>
                    @elseif($user->role === 'admin')
                        <span class="badge bg-danger">Administrador</span>
                    @endif
                    
                    <hr>
                    
                    <p class="mb-1"><strong>Usuario desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                    <p class="mb-1"><strong>Última actualización:</strong> {{ $user->updated_at->format('d/m/Y') }}</p>
                    
                    @if($isOwnProfile)
                        <div class="mt-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-user-edit me-1"></i> Editar mi perfil
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Detalles adicionales -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Información Detallada</h5>
                    @if($isOwnProfile)
                        <a href="{{ route('profile.edit') }}#info" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Editar información
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($user->role === 'student' && isset($user->studentProfile))
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold">Nivel</h6>
                                <p>{{ $user->studentProfile->level ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold">Puntos XP</h6>
                                <p>{{ $user->studentProfile->xp_points ?? 0 }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold">Misiones completadas</h6>
                                <p>{{ $user->studentProfile->total_missions_completed ?? 0 }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold">Edad</h6>
                                <p>{{ $user->studentProfile->age ?? 'No especificada' }}</p>
                            </div>
                        </div>
                    @elseif($user->role === 'teacher' && isset($user->teacherProfile))
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold">Institución</h6>
                                <p>{{ $user->teacherProfile->institution ?? 'No especificada' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold">Clases activas</h6>
                                <p>{{ $user->classrooms()->where('is_active', true)->count() }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="fw-bold">Total de estudiantes</h6>
                                <p>{{ $user->teacherProfile->total_students ?? 0 }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No hay información adicional disponible.</p>
                    @endif
                </div>
            </div>
            
            @if($isOwnProfile)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Opciones de cuenta</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('profile.edit') }}#password" class="btn btn-outline-secondary w-100 h-100 py-3 d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-lock fa-2x mb-2"></i>
                                    <span>Cambiar contraseña</span>
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('profile.edit') }}#notifications" class="btn btn-outline-secondary w-100 h-100 py-3 d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-bell fa-2x mb-2"></i>
                                    <span>Notificaciones</span>
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('profile.edit') }}#danger" class="btn btn-outline-danger w-100 h-100 py-3 d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <span>Eliminar cuenta</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="d-flex justify-content-end mt-3 gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection 