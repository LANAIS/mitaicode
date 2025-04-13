@extends('layouts.app')

@section('title', 'Crear Nueva Clase')

@section('header', 'Crear Nueva Clase')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Información de la Clase</h5>

                    <form action="{{ route('classrooms.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nombre de la Clase *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required>
                            
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="access_code" class="form-label fw-semibold">Código de Acceso</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('access_code') is-invalid @enderror" 
                                    id="access_code" name="access_code" value="{{ old('access_code') }}" 
                                    placeholder="Dejar en blanco para generar automáticamente">
                                <button class="btn btn-outline-secondary" type="button" id="generate-code">Generar</button>
                            </div>
                            <div class="form-text">Los estudiantes usarán este código para unirse a la clase.</div>
                            
                            @error('access_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                    {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Clase activa
                                </label>
                            </div>
                            <div class="form-text">Las clases inactivas no serán visibles para los estudiantes.</div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Crear Clase</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generador de código aleatorio
            const generateCodeBtn = document.getElementById('generate-code');
            const accessCodeInput = document.getElementById('access_code');
            
            generateCodeBtn.addEventListener('click', function() {
                // Generar un código alfanumérico de 8 caracteres
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let code = '';
                for (let i = 0; i < 8; i++) {
                    code += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                accessCodeInput.value = code;
            });
        });
    </script>
    @endpush
@endsection 