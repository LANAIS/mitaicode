@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white p-3">
                    <h3 class="mb-0">Mi Perfil</h3>
                    <p class="text-muted mb-0">Administra tu información personal y configura tu cuenta</p>
                </div>
                
                <div class="card-body p-0">
                    <!-- Navegación por pestañas Bootstrap -->
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                                <i class="fas fa-user me-2"></i> Información Personal
                            </button>
                        </li>
                        @if(auth()->user()->role === 'teacher')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab" aria-controls="analytics" aria-selected="false">
                                <i class="fas fa-chart-bar me-2"></i> Analíticas
                            </button>
                        </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                <i class="fas fa-lock me-2"></i> Contraseña
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                                <i class="fas fa-bell me-2"></i> Notificaciones
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="danger-tab" data-bs-toggle="tab" data-bs-target="#danger" type="button" role="tab" aria-controls="danger" aria-selected="false">
                                <i class="fas fa-exclamation-triangle me-2"></i> Zona de Peligro
                            </button>
                        </li>
                    </ul>
                    
                    <!-- Contenido de las pestañas -->
                    <div class="tab-content p-4" id="profileTabsContent">
                        <!-- Información Personal -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                        
                        @if(auth()->user()->role === 'teacher')
                        <!-- Analíticas para Profesores -->
                        <div class="tab-pane fade" id="analytics" role="tabpanel" aria-labelledby="analytics-tab">
                            @include('profile.partials.teacher-challenge-analytics')
                        </div>
                        @endif
                        
                        <!-- Contraseña -->
                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            @include('profile.partials.update-password-form')
                        </div>
                        
                        <!-- Notificaciones -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                            @include('profile.partials.update-notification-preferences-form')
                        </div>
                        
                        <!-- Zona de Peligro -->
                        <div class="tab-pane fade" id="danger" role="tabpanel" aria-labelledby="danger-tab">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener hash de la URL para activar la pestaña correspondiente
        let hash = window.location.hash;
        if (hash) {
            // Eliminar el # inicial del hash para obtener el id
            let tabId = hash.substring(1);
            // Activar la pestaña correspondiente
            const tabEl = document.querySelector(`#profileTabs button[data-bs-target="#${tabId}"]`);
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
        
        // Guardar la pestaña activa al cambiar
        const tabs = document.querySelectorAll('#profileTabs button');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (e) {
                // Actualizar URL con hash de la pestaña activa
                const targetId = e.target.getAttribute('data-bs-target').substring(1);
                window.history.replaceState(null, null, `#${targetId}`);
            });
        });
        
        // Configurar los mensajes de alerta para desaparecer después de 2 segundos
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = 0;
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 1000);
            }, 2000);
        });
    });
</script>
@endsection 