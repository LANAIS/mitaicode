@extends('layouts.app')

@section('title', 'Registro')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="h3 fw-bold text-center mb-4">Crear Cuenta</h2>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Nombre de Usuario -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">
                        Nombre de Usuario
                    </label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="form-control @error('username') is-invalid @enderror">
                    
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <!-- Nombre -->
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label fw-semibold">
                            Nombre
                        </label>
                        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required
                            class="form-control @error('first_name') is-invalid @enderror">
                        
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Apellido -->
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label fw-semibold">
                            Apellido
                        </label>
                        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                            class="form-control @error('last_name') is-invalid @enderror">
                        
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        Correo Electrónico
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="form-control @error('email') is-invalid @enderror">
                    
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Tipo de Usuario -->
                <div class="mb-3">
                    <label for="role" class="form-label fw-semibold">
                        Tipo de Usuario
                    </label>
                    <select id="role" name="role" required
                        class="form-select @error('role') is-invalid @enderror">
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Estudiante</option>
                        <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Profesor</option>
                    </select>
                    
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Campos adicionales para Estudiantes -->
                <div id="student-fields" class="mb-3" style="{{ old('role') == 'teacher' ? 'display: none;' : '' }}">
                    <div class="card card-body bg-light mb-3">
                        <div class="mb-3">
                            <label for="age" class="form-label fw-semibold">
                                Edad
                            </label>
                            <input id="age" type="number" name="age" value="{{ old('age') }}"
                                class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label for="parent_email" class="form-label fw-semibold">
                                Correo del Padre/Tutor (Opcional)
                            </label>
                            <input id="parent_email" type="email" name="parent_email" value="{{ old('parent_email') }}"
                                class="form-control">
                        </div>
                    </div>
                </div>
                
                <!-- Campos adicionales para Profesores -->
                <div id="teacher-fields" class="mb-3" style="{{ old('role') == 'student' || old('role') == null ? 'display: none;' : '' }}">
                    <div class="card card-body bg-light mb-3">
                        <div class="mb-3">
                            <label for="institution" class="form-label fw-semibold">
                                Institución
                            </label>
                            <input id="institution" type="text" name="institution" value="{{ old('institution') }}"
                                class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Contraseña -->
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-semibold">
                            Contraseña
                        </label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="form-control @error('password') is-invalid @enderror">
                        
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Confirmar Contraseña -->
                    <div class="col-md-6 mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            Confirmar Contraseña
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="form-control">
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary">
                        Registrarse
                    </button>
                    
                    <a href="{{ route('login') }}" class="text-decoration-none text-primary fw-semibold">
                        ¿Ya tienes cuenta?
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Script para cambiar campos según el tipo de usuario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const studentFields = document.getElementById('student-fields');
            const teacherFields = document.getElementById('teacher-fields');
            
            roleSelect.addEventListener('change', function() {
                if (this.value === 'student') {
                    studentFields.style.display = 'block';
                    teacherFields.style.display = 'none';
                } else if (this.value === 'teacher') {
                    studentFields.style.display = 'none';
                    teacherFields.style.display = 'block';
                }
            });
        });
    </script>
@endsection 