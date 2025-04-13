<section class="mb-4">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Actualizar Contraseña') }}
        </h2>

        <p class="mt-1 text-muted">
            {{ __('Asegúrate de que tu cuenta esté usando una contraseña larga y aleatoria para mantenerte seguro.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="current_password" class="form-label">{{ __('Contraseña actual') }}</label>
            <input id="current_password" name="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" />
            @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Nueva contraseña') }}</label>
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" />
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Confirmar contraseña') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="new-password" />
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">
                {{ __('Guardar') }}
            </button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success d-inline-block ms-3 mb-0 py-1 px-2">
                    {{ __('Guardado.') }}
                </div>
            @endif
        </div>
    </form>
</section> 