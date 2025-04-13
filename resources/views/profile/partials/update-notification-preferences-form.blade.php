<section class="mb-4">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Preferencias de Notificación') }}
        </h2>

        <p class="mt-1 text-muted">
            {{ __('Configura qué tipos de notificaciones deseas recibir en tu correo electrónico.') }}
        </p>
    </header>

    <form id="notification-preferences-form" method="post" action="{{ route('profile.notification-preferences.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="form-check mb-3">
            <input id="receive_emails" name="receive_emails" type="checkbox" class="form-check-input" 
                {{ $preferences && $preferences->receive_emails ? 'checked' : '' }}>
            <label for="receive_emails" class="form-check-label">
                {{ __('Recibir notificaciones por email') }}
            </label>
            <p class="text-muted small">
                {{ __('Opción principal para recibir o no cualquier tipo de email') }}
            </p>
        </div>

        <div id="notification-types" class="{{ $preferences && $preferences->receive_emails ? '' : 'opacity-50' }}">
            <h6 class="mb-3">{{ __('Tipos de notificación:') }}</h6>

            <div class="ms-4">
                <div class="form-check mb-3">
                    <input id="receive_welcome_emails" name="receive_welcome_emails" type="checkbox" class="form-check-input" 
                        {{ $preferences && $preferences->receive_welcome_emails ? 'checked' : '' }}>
                    <label for="receive_welcome_emails" class="form-check-label">
                        {{ __('Emails de bienvenida y onboarding') }}
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input id="receive_reminder_emails" name="receive_reminder_emails" type="checkbox" class="form-check-input" 
                        {{ $preferences && $preferences->receive_reminder_emails ? 'checked' : '' }}>
                    <label for="receive_reminder_emails" class="form-check-label">
                        {{ __('Recordatorios de desafíos y progreso') }}
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input id="receive_inactive_emails" name="receive_inactive_emails" type="checkbox" class="form-check-input" 
                        {{ $preferences && $preferences->receive_inactive_emails ? 'checked' : '' }}>
                    <label for="receive_inactive_emails" class="form-check-label">
                        {{ __('Recordatorios por inactividad') }}
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input id="receive_new_content_emails" name="receive_new_content_emails" type="checkbox" class="form-check-input" 
                        {{ $preferences && $preferences->receive_new_content_emails ? 'checked' : '' }}>
                    <label for="receive_new_content_emails" class="form-check-label">
                        {{ __('Notificaciones de nuevo contenido') }}
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input id="receive_marketing_emails" name="receive_marketing_emails" type="checkbox" class="form-check-input" 
                        {{ $preferences && $preferences->receive_marketing_emails ? 'checked' : '' }}>
                    <label for="receive_marketing_emails" class="form-check-label">
                        {{ __('Promociones, eventos y marketing') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center mt-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Guardar preferencias') }}
            </button>

            @if (session('status') === 'notification-preferences-updated')
                <div class="alert alert-success d-inline-block ms-3 mb-0 py-1 px-2">
                    {{ __('Guardado.') }}
                </div>
            @endif
        </div>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainCheckbox = document.getElementById('receive_emails');
        const notificationTypes = document.getElementById('notification-types');
        
        mainCheckbox.addEventListener('change', function() {
            if (this.checked) {
                notificationTypes.classList.remove('opacity-50');
            } else {
                notificationTypes.classList.add('opacity-50');
            }
        });
    });
</script> 