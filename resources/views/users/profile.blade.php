@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('header', 'Editar Perfil')

@section('content')
    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-3">
                        @php
                            // Obtener el avatar del usuario o crear uno si no existe
                            $avatar = \App\Models\UserAvatar::getOrCreate($user->user_id);
                        @endphp
                        <img src="{{ $avatar->getAvatarImageUrl() }}" 
                            alt="{{ $user->first_name }} {{ $user->last_name }}" 
                            class="rounded-circle img-fluid mx-auto d-block" style="width: 120px; height: 120px; border: 3px solid #4e73df;">
                    </div>
                    <h5 class="card-title">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p class="card-text text-muted">{{ $user->email }}</p>
                    
                    @if($user->role === 'student')
                        <span class="badge bg-primary">Estudiante</span>
                        <div class="mt-3">
                            <a href="{{ route('store.avatar') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-user-edit me-1"></i> Personalizar Avatar
                            </a>
                        </div>
                    @elseif($user->role === 'teacher')
                        <span class="badge bg-success">Profesor</span>
                    @elseif($user->role === 'admin')
                        <span class="badge bg-danger">Administrador</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Información Personal</h5>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Nombre</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                    id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                
                                @error('first_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Apellido</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                    id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                
                                @error('last_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <hr class="my-4">
                        <h5 class="mb-3">Cambiar Contraseña</h5>
                        <p class="text-muted mb-3">Dejar en blanco para mantener la contraseña actual</p>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                id="password" name="password">
                            
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" 
                                id="password_confirmation" name="password_confirmation">
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 