<section class="mb-4">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Información del Perfil') }}
        </h2>

        <p class="mt-1 text-muted">
            {{ __('Actualiza la información de tu perfil y tu dirección de correo electrónico.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="username" class="form-label">{{ __('Nombre de usuario') }}</label>
            <input id="username" name="username" type="text" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required autofocus autocomplete="username" />
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="first_name" class="form-label">{{ __('Nombre') }}</label>
            <input id="first_name" name="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $user->first_name) }}" required />
            @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">{{ __('Apellido') }}</label>
            <input id="last_name" name="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->last_name) }}" required />
            @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="email" />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-muted small">
                    {{ __('Tu dirección de email no está verificada.') }}
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2">
                        {{ __('Se ha enviado un nuevo enlace de verificación a tu dirección de email.') }}
                    </div>
                @endif
            @endif
        </div>

        @if ($user->role === 'student' && $user->studentProfile)
        <div class="mb-3">
            <label for="age" class="form-label">{{ __('Edad') }}</label>
            <input id="age" name="age" type="number" class="form-control @error('age') is-invalid @enderror" value="{{ old('age', $user->studentProfile->age) }}" min="1" max="120" />
            @error('age')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @elseif ($user->role === 'teacher' && $user->teacherProfile)
        <div class="mb-3">
            <label for="institution" class="form-label">{{ __('Institución') }}</label>
            <input id="institution" name="institution" type="text" class="form-control @error('institution') is-invalid @enderror" value="{{ old('institution', $user->teacherProfile->institution) }}" />
            @error('institution')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @endif

        <div class="mb-3">
            <label for="avatar" class="form-label">{{ __('Avatar') }}</label>
            <input id="avatar" name="avatar" type="file" class="form-control @error('avatar') is-invalid @enderror" accept="image/*" />
            @error('avatar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            
            @if ($user->avatar_url)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $user->avatar_url) }}" alt="Avatar actual" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                <p class="text-muted small mt-1">{{ __('Avatar actual') }}</p>
            </div>
            @endif
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">
                {{ __('Guardar') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success d-inline-block ms-3 mb-0 py-1 px-2">
                    {{ __('Guardado.') }}
                </div>
            @endif
        </div>
    </form>
</section> 