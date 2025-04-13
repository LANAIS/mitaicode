<section class="mb-4">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Eliminar Cuenta') }}
        </h2>

        <p class="mt-1 text-muted">
            {{ __('Una vez que tu cuenta sea eliminada, todos tus recursos y datos serán eliminados permanentemente. Antes de eliminar tu cuenta, por favor descarga cualquier dato o información que desees conservar.') }}
        </p>
    </header>

    <div class="mt-3">
        <button
            type="button"
            class="btn btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#confirm-user-deletion"
        >
            {{ __('Eliminar cuenta') }}
        </button>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">{{ __('¿Estás seguro de que quieres eliminar tu cuenta?') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="delete-account-form" method="post" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('delete')

                        <p class="text-muted">
                            {{ __('Una vez que tu cuenta sea eliminada, todos tus recursos y datos serán eliminados permanentemente. Por favor ingresa tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.') }}
                        </p>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Contraseña') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="{{ __('Ingresa tu contraseña') }}"
                            />
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('Cancelar') }}
                    </button>
                    <button 
                        type="button" 
                        class="btn btn-danger" 
                        onclick="document.getElementById('delete-account-form').submit();"
                    >
                        {{ __('Eliminar cuenta') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</section> 