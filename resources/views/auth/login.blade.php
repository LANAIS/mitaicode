@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="h3 fw-bold text-center mb-4">Iniciar Sesión</h2>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        Correo Electrónico
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        class="form-control @error('email') is-invalid @enderror">
                    
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Contraseña -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">
                        Contraseña
                    </label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="form-control @error('password') is-invalid @enderror">
                    
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Recordarme -->
                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="form-check-input">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary">
                        Iniciar Sesión
                    </button>
                    
                    <a href="{{ route('register') }}" class="text-decoration-none text-primary fw-semibold">
                        ¿No tienes cuenta?
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection 