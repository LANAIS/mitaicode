<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/images/mitai-logo-128x128.svg') }}" alt="MitaiCode" height="30">
            <span class="ms-2 fw-bold text-primary">MitaiCode</span>
        </a>
        
        <!-- Botón de navegación móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Enlaces de navegación -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>
                
                @if(auth()->user() && auth()->user()->teacherProfile)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('classrooms.*') ? 'active fw-semibold' : '' }}" href="{{ route('classrooms.index') }}">
                            Mis Clases
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('challenges.*') ? 'active fw-semibold' : '' }}" href="{{ route('challenges.index') }}">
                            Desafíos
                        </a>
                    </li>
                @endif
                
                @if(auth()->user() && auth()->user()->role == 'teacher')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('challenges.*') ? 'active fw-semibold' : '' }}" href="{{ route('challenges.index') }}">
                            Desafíos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teacher.hackathons') ? 'active fw-semibold' : '' }}" href="{{ route('teacher.hackathons') }}">
                            <i class="fas fa-award fa-fw me-1"></i> Evaluar Hackathones
                            @php
                                try {
                                    $pendingCount = 0;
                                    if (Schema::hasTable('hackathon_deliverables') && Schema::hasTable('hackathons')) {
                                        $hackathonIds = App\Models\Hackathon::where('created_by', auth()->id())
                                            ->orWhereHas('judges', function($query) {
                                                $query->where('user_id', auth()->id());
                                            })
                                            ->pluck('id');
                                        
                                        $pendingCount = App\Models\HackathonDeliverable::whereHas('team', function($query) use ($hackathonIds) {
                                            $query->whereIn('hackathon_id', $hackathonIds);
                                        })
                                        ->whereNull('evaluated_at')
                                        ->count();
                                    }
                                } catch (\Throwable $e) {
                                    $pendingCount = 0;
                                }
                            @endphp
                            @if($pendingCount > 0)
                                <span class="badge rounded-pill bg-danger">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                @endif
                
                @if(auth()->user() && auth()->user()->studentProfile)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.challenges.*') ? 'active fw-semibold' : '' }}" href="{{ route('student.challenges.index') }}">
                            Desafíos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('hackathons*') ? 'active fw-semibold' : '' }}" href="{{ route('hackathons.index') }}">
                            <i class="fas fa-trophy fa-fw me-1"></i> Hackathones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gamification.leaderboards.*') ? 'active fw-semibold' : '' }}" href="{{ route('gamification.leaderboards.index') }}">
                            Clasificación
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('store.*') ? 'active fw-semibold' : '' }}" href="{{ route('store.index') }}">
                            Tienda
                        </a>
                    </li>
                @endif
            </ul>
            
            <!-- Menú de usuario -->
            <div class="d-flex align-items-center">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;" alt="Avatar">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->first_name . '+' . auth()->user()->last_name) }}&size=30&background=e9ecef&color=6c757d" class="rounded-circle me-2" width="30" height="30" alt="Avatar">
                            @endif
                            <span class="text-muted">{{ auth()->user()->first_name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('my.profile') }}">
                                    <i class="fas fa-user fa-fw me-2"></i> Ver perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-edit fa-fw me-2"></i> Editar perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-fw me-2"></i> Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <!-- Se han eliminado los botones de inicio de sesión y registro -->
                @endauth
            </div>
        </div>
    </div>
</nav> 