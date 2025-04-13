@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('styles')
<style>
    .text-bronze {
        color: #cd7f32;
    }
    
    .ranking-badge {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .medal-1 .fa-medal {
        font-size: 1.5rem;
        filter: drop-shadow(0 0 3px rgba(255, 215, 0, 0.5));
    }
    
    .medal-2 .fa-medal {
        font-size: 1.4rem;
        filter: drop-shadow(0 0 2px rgba(192, 192, 192, 0.5));
    }
    
    .medal-3 .fa-medal {
        font-size: a1.3rem;
        filter: drop-shadow(0 0 2px rgba(205, 127, 50, 0.5));
    }
    
    .bg-gradient-primary {
        background: linear-gradient(to right, #0d6efd, #0a58ca);
    }
    
    /* Estilos para la tarjeta de perfil */
    .profile-card {
        border-top: 4px solid;
        border-image: linear-gradient(to right, #4e73df, #36b9cc) 1;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
    }
    
    .profile-stat {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 8px;
        background-color: #f8f9fc;
        transition: background-color 0.2s ease;
    }
    
    .profile-stat:hover {
        background-color: #eaecf4;
    }
    
    .profile-stat i {
        filter: drop-shadow(0 0 1px rgba(0, 0, 0, 0.2));
    }
</style>
@endsection

@section('content')
    @php
        use Illuminate\Support\Facades\Schema;
        use Illuminate\Support\Facades\Route;
        use Illuminate\Support\Facades\DB;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Str;
    @endphp
    
    <div class="mb-4">
        <h3 class="h5 fw-semibold mb-2">
            ¡Bienvenido {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}!
        </h3>
        <p class="text-muted">
            @if(auth()->user()->role === 'student')
                Bienvenido a tu portal de Hackathones y Desafíos. ¡Participa en competencias, aprende y mejora tus habilidades!
            @elseif(auth()->user()->role === 'teacher')
                Eres un profesor en MitaiCode. Desde aquí puedes gestionar tus clases, hackathones y los estudiantes que participan en ellas.
            @else
                Eres un administrador en MitaiCode. Tienes acceso completo a la plataforma.
            @endif
        </p>
    </div>

    <div class="row g-4">
        <!-- Estadísticas del usuario -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 profile-card">
                <div class="card-body">
                    <h4 class="card-title fw-semibold h5 mb-3"><i class="fas fa-user-circle text-primary me-2"></i>Mi Perfil</h4>
                    
                    @if(auth()->user()->role === 'student' && auth()->user()->studentProfile)
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-trophy text-warning me-2"></i>Nivel:</span>
                            <span class="fw-medium ms-auto">{{ auth()->user()->studentProfile->level }}</span>
                        </div>
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-star text-primary me-2"></i>Puntos XP:</span>
                            <span class="fw-medium ms-auto">{{ auth()->user()->studentProfile->xp_points }}</span>
                        </div>
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-tasks text-success me-2"></i>Misiones completadas:</span>
                            <span class="fw-medium ms-auto">{{ auth()->user()->studentProfile->total_missions_completed }}</span>
                        </div>
                        @php
                            $completedChallenges = App\Models\ChallengeProgress::where('user_id', auth()->id())
                                ->where('is_completed', true)
                                ->count();
                        @endphp
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-code text-info me-2"></i>Desafíos completados:</span>
                            <span class="fw-medium ms-auto">{{ $completedChallenges }}</span>
                        </div>
                        @if(isset($streak) && $streak)
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-fire text-danger me-2"></i>Racha actual:</span>
                            <span class="fw-medium ms-auto">{{ $streak->current_streak }} días</span>
                        </div>
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-award text-warning me-2"></i>Mejor racha:</span>
                            <span class="fw-medium ms-auto">{{ $streak->longest_streak }} días</span>
                        </div>
                        @else
                        @php
                            $userStreak = App\Models\UserStreak::where('user_id', auth()->id())->first();
                        @endphp
                        @if($userStreak)
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-fire text-danger me-2"></i>Racha actual:</span>
                            <span class="fw-medium ms-auto">{{ $userStreak->current_streak }} días</span>
                        </div>
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-award text-warning me-2"></i>Mejor racha:</span>
                            <span class="fw-medium ms-auto">{{ $userStreak->longest_streak }} días</span>
                        </div>
                        @else
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-fire text-danger me-2"></i>Racha actual:</span>
                            <span class="fw-medium ms-auto">0 días</span>
                        </div>
                        @endif
                        @endif
                    @elseif(auth()->user()->role === 'teacher' && auth()->user()->teacherProfile)
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-university text-info me-2"></i>Institución:</span>
                            <span class="fw-medium ms-auto">{{ auth()->user()->teacherProfile->institution ?: 'No especificada' }}</span>
                        </div>
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-chalkboard-teacher text-success me-2"></i>Clases activas:</span>
                            <span class="fw-medium ms-auto">{{ auth()->user()->classrooms()->where('is_active', true)->count() }}</span>
                        </div>
                        @php
                            $hackathonsCount = 0;
                            try {
                                if (Schema::hasTable('hackathons')) {
                                    $hackathonsCount = App\Models\Hackathon::where('created_by', auth()->id())->where('status', 'active')->count();
                                }
                            } catch (\Throwable $e) {
                                // No hacer nada si hay error
                            }
                        @endphp
                        <div class="profile-stat">
                            <span class="text-muted"><i class="fas fa-trophy text-warning me-2"></i>Hackathones activos:</span>
                            <span class="fw-medium ms-auto">{{ $hackathonsCount }}</span>
                        </div>
                    @endif
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('users.show', auth()->id()) }}" class="btn btn-outline-primary">
                            <i class="fas fa-user me-1"></i> Ver perfil completo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="col-md-6 col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title fw-semibold h5 mb-3">Accesos Rápidos</h4>
                    
                    <div class="row g-3">
                        @if(auth()->user()->role === 'student')
                            @php
                                $activeHackathon = null;
                                try {
                                    if (Schema::hasTable('hackathons')) {
                                        $activeHackathon = App\Models\Hackathon::where('status', 'active')
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                                    }
                                } catch (\Throwable $e) {
                                    // No hacer nada si hay error
                                }
                            @endphp
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <a href="{{ route('hackathons.index') }}" class="btn btn-outline-primary d-flex flex-column align-items-center p-3 h-100 w-100">
                                    <i class="fas fa-trophy fs-4 mb-2"></i>
                                    <span>Hackathones</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <a href="{{ route('student.challenges.index') }}" class="btn btn-outline-success d-flex flex-column align-items-center p-3 h-100 w-100">
                                    <i class="fas fa-code fs-4 mb-2"></i>
                                    <span>Desafíos</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <a href="{{ route('gamification.leaderboards.index') }}" class="btn btn-outline-warning d-flex flex-column align-items-center p-3 h-100 w-100">
                                    <i class="fas fa-medal fs-4 mb-2"></i>
                                    <span>Clasificación</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <a href="{{ route('store.index') }}" class="btn btn-outline-danger d-flex flex-column align-items-center p-3 h-100 w-100">
                                    <i class="fas fa-shopping-cart fs-4 mb-2"></i>
                                    <span>Tienda</span>
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->role === 'teacher')
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <a href="{{ route('classrooms.index') }}" class="btn btn-outline-primary d-flex flex-column align-items-center p-3 h-100 w-100">
                                    <i class="fas fa-chalkboard-teacher fs-4 mb-2"></i>
                                    <span>Mis Clases</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <a href="{{ route('hackathons.index') }}" class="btn btn-outline-warning d-flex flex-column align-items-center p-3 h-100 w-100">
                                    <i class="fas fa-trophy fs-4 mb-2"></i>
                                    <span>Hackathones</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <a href="{{ route('challenges.index') }}" class="btn btn-outline-success d-flex flex-column align-items-center p-3 h-100 w-100">
                                    <i class="fas fa-code fs-4 mb-2"></i>
                                    <span>Desafíos</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <a href="{{ route('gamification.leaderboards.index') }}" class="btn btn-outline-warning d-flex flex-column align-items-center p-3 h-100 w-100">
                                    <i class="fas fa-medal fs-4 mb-2"></i>
                                    <span>Clasificación</span>
                                </a>
                            </div>
                            @php
                                $isJudgeOfAnyHackathon = false;
                                $activeHackathonForJudge = null;
                                try {
                                    if (Schema::hasTable('hackathon_judges')) {
                                        $judgeCount = DB::table('hackathon_judges')
                                            ->where('user_id', auth()->id())
                                            ->count();
                                        $isJudgeOfAnyHackathon = $judgeCount > 0;
                                        
                                        if ($isJudgeOfAnyHackathon) {
                                            $activeHackathonForJudge = App\Models\Hackathon::whereHas('judges', function($query) {
                                                $query->where('user_id', auth()->id());
                                            })->where('status', 'active')
                                              ->orderBy('created_at', 'desc')
                                              ->first();
                                        }
                                    }
                                } catch (\Throwable $e) {
                                    // No hacer nada si hay error
                                }
                            @endphp
                            @if($isJudgeOfAnyHackathon)
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <a href="{{ $activeHackathonForJudge && isset($activeHackathonForJudge->hackathon_id) 
                                    ? route('hackathons.deliverables.evaluate', ['id' => $activeHackathonForJudge->hackathon_id]) 
                                    : route('hackathons.index') }}" 
                                    class="btn btn-outline-danger d-flex flex-column align-items-center p-3 h-100 w-100">
                                    <i class="fas fa-clipboard-check fs-4 mb-2"></i>
                                    <span>Evaluar Entregables</span>
                                    @if($activeHackathonForJudge && isset($activeHackathonForJudge->pendingDeliverablesCount) && $activeHackathonForJudge->pendingDeliverablesCount() > 0)
                                        <span class="badge bg-danger rounded-pill mt-1">{{ $activeHackathonForJudge->pendingDeliverablesCount() }}</span>
                                    @endif
                                </a>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'student')
        <!-- Sección de Hackathones para estudiantes -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Hackathones <span class="badge bg-danger ms-2">Nuevo</span></h5>
                    <a href="{{ route('hackathons.index') }}" class="btn btn-sm btn-light">Ver todos</a>
                </div>
                <div class="card-body">
                    @php
                        $hackathons = [];
                        try {
                            if (Schema::hasTable('hackathons')) {
                                $hackathons = App\Models\Hackathon::where('status', 'active')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(2)
                                    ->get();
                                    
                                $myTeams = [];
                                $teamIdsByHackathon = [];
                                if (Auth::check()) {
                                    $teamData = DB::table('hackathon_team_user')
                                        ->join('hackathon_teams', 'hackathon_team_user.team_id', '=', 'hackathon_teams.team_id')
                                        ->where('hackathon_team_user.user_id', Auth::id())
                                        ->select('hackathon_teams.hackathon_id', 'hackathon_teams.team_id')
                                        ->get();
                                    
                                    $myTeams = $teamData->pluck('hackathon_id')->toArray();
                                    $teamIdsByHackathon = $teamData->pluck('team_id', 'hackathon_id')->toArray();
                                }
                            }
                        } catch (\Throwable $e) {
                            // No hacer nada si hay error
                        }
                    @endphp
                    
                    @if(count($hackathons) > 0)
                        @if(!count(array_intersect($myTeams, $hackathons->pluck('hackathon_id')->toArray())))
                        <div class="alert alert-primary mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="alert-heading mb-1"><i class="fas fa-bell me-2"></i>¡Inscríbete ahora!</h5>
                                <p class="mb-0">Hay hackathones activos esperando tu participación. ¡Inscríbete o forma tu propio equipo!</p>
                            </div>
                            @if(isset($hackathons[0]->hackathon_id))
                            <a href="{{ route('student.hackathons.join', $hackathons[0]->hackathon_id) }}" class="btn btn-primary btn-lg ms-3">
                                <i class="fas fa-sign-in-alt me-1"></i> Inscribirse
                            </a>
                            @endif
                        </div>
                        @endif
                        <div class="row g-4">
                            @foreach($hackathons as $hackathon)
                                <div class="col-lg-6">
                                    <div class="card h-100 hackathon-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="m-0">{{ $hackathon->title }}</h5>
                                            <span class="badge bg-primary">
                                                En progreso - Ronda {{ $hackathon->current_round ?? 1 }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                @foreach(explode(',', $hackathon->tags ?? 'Hackathon') as $tag)
                                                    <span class="badge rounded-pill bg-info me-2">{{ trim($tag) }}</span>
                                                @endforeach
                                            </div>
                                            
                                            <div class="hackathon-info mb-3">
                                                <div><i class="fas fa-calendar me-2"></i> <strong>Fecha:</strong> 
                                                    {{ $hackathon->start_date ? date('d M', strtotime($hackathon->start_date)) : '' }} - 
                                                    {{ $hackathon->end_date ? date('d M, Y', strtotime($hackathon->end_date)) : '' }}
                                                </div>
                                                <div><i class="fas fa-users me-2"></i> <strong>Equipos:</strong> 
                                                    {{ $hackathon->teams()->count() }} equipos participando
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-layer-group me-2"></i> <strong>Progreso:</strong>
                                                    <div class="progress ms-2" style="height: 8px; width: 150px;">
                                                        @php
                                                            $totalRounds = max(1, $hackathon->total_rounds ?? 3);
                                                            $currentRound = $hackathon->current_round ?? 1;
                                                            $progress = ($currentRound / $totalRounds) * 100;
                                                        @endphp
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                             role="progressbar" 
                                                             style="width: {{ $progress }}%" 
                                                             aria-valuenow="{{ $progress }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100"></div>
                                                    </div>
                                                    <span class="ms-2">Ronda {{ $currentRound }} de {{ $totalRounds }}</span>
                                                </div>
                                            </div>
                                            
                                            <p class="card-text">{{ Str::limit($hackathon->description, 120) }}</p>
                                            
                                            <hr>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @if(in_array($hackathon->hackathon_id, $myTeams ?? []))
                                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i> Ya estás participando</span>
                                                    @else
                                                        <span class="text-danger"><i class="fas fa-info-circle me-1"></i> No estás participando</span>
                                                    @endif
                                                </div>
                                                
                                                @if(in_array($hackathon->hackathon_id, $myTeams ?? []))
                                                    <a href="{{ route('student.hackathons.team', $teamIdsByHackathon[$hackathon->hackathon_id] ?? $hackathon->team_id ?? $hackathon->hackathon_id) }}" class="btn btn-primary">
                                                        <i class="fas fa-eye me-1"></i> Ver mi equipo
                                                    </a>
                                                @else
                                                    @if(isset($hackathon->hackathon_id))
                                                    <a href="{{ route('student.hackathons.join', $hackathon->hackathon_id) }}" class="btn btn-success btn-lg">
                                                        <i class="fas fa-plus me-1"></i> Unirse o crear equipo
                                                    </a>
                                                    @else
                                                    <button class="btn btn-secondary" disabled>ID no disponible</button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i> No hay hackathones activos actualmente. Consulta pronto para nuevas oportunidades.
                        </div>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                            <h5>Próximamente nuevos hackathones</h5>
                            <p class="text-muted">Estamos preparando nuevos hackathones para ti. Mantente al tanto de nuevas oportunidades.</p>
                            <a href="{{ route('hackathons.index') }}" class="btn btn-outline-primary mt-2">
                                <i class="fas fa-history me-1"></i> Ver historial de hackathones
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->role === 'student')
        <!-- Mensaje sobre Hackathones en lugar de la Clasificación -->
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">¿Por qué participar en Hackathones?</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="p-3">
                                <i class="fas fa-rocket fa-3x text-primary mb-3"></i>
                                <h5>Aprende en equipo</h5>
                                <p class="text-muted">Colabora con otros estudiantes para resolver problemas complejos y desarrollar soluciones innovadoras.</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="p-3">
                                <i class="fas fa-brain fa-3x text-success mb-3"></i>
                                <h5>Desarrolla habilidades</h5>
                                <p class="text-muted">Mejora tus habilidades técnicas, de trabajo en equipo y de presentación en un entorno competitivo.</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3">
                                <i class="fas fa-trophy fa-3x text-warning mb-3"></i>
                                <h5>Gana premios</h5>
                                <p class="text-muted">Compite por premios, reconocimiento y la oportunidad de mostrar tus proyectos a la comunidad.</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('hackathons.index') }}" class="btn btn-primary">
                            Explora los hackathones disponibles <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->role === 'teacher')
        <!-- Gestión de Hackathones -->
        <div class="col-md-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title fw-semibold h5 mb-0">Mis Hackathones</h4>
                </div>
                <div class="card-body">
                    @php
                        $recentHackathons = collect([]);
                        try {
                            if (Schema::hasTable('hackathons')) {
                                $recentHackathons = App\Models\Hackathon::where('created_by', auth()->id())
                                    ->orderBy('created_at', 'desc')
                                    ->take(3)
                                    ->get();
                            }
                        } catch (\Throwable $e) {
                            // No hacer nada si hay error
                        }
                    @endphp
                    
                    @if($recentHackathons->count() > 0 && Route::has('hackathons.show'))
                        <div class="list-group">
                            @foreach($recentHackathons as $hackathon)
                                @if(isset($hackathon->hackathon_id))
                                <a href="{{ route('hackathons.show', ['id' => $hackathon->hackathon_id]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                @else
                                <a href="{{ route('hackathons.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                @endif
                                    <div>
                                        <h6 class="mb-1">{{ $hackathon->title }}</h6>
                                        <small class="text-muted">
                                            {{ $hackathon->teams()->count() }} equipos | 
                                            @if($hackathon->status === 'active')
                                                <span class="text-success">Activo</span>
                                            @elseif($hackathon->status === 'finished')
                                                <span class="text-secondary">Finalizado</span>
                                            @else
                                                <span class="text-warning">Pendiente</span>
                                            @endif
                                        </small>
                                    </div>
                                    <div class="d-flex">
                                        @if(Route::has('hackathons.rounds.index') && isset($hackathon->hackathon_id))
                                        <a href="{{ route('hackathons.rounds.index', $hackathon->hackathon_id) }}" class="btn btn-sm btn-outline-primary me-1" title="Gestionar rondas">
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                        @endif
                                        @if(Route::has('hackathons.deliverables.evaluate') && isset($hackathon->hackathon_id))
                                        <a href="{{ route('hackathons.deliverables.evaluate', $hackathon->hackathon_id) }}" class="btn btn-sm btn-outline-success" title="Evaluar entregables">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-3 text-center">
                            <a href="{{ route('hackathons.index') }}" class="btn btn-sm btn-primary">Ver todos mis hackathones</a>
                        </div>
                    @else
                        <p class="text-muted">
                            No has creado ningún hackathon todavía o necesitas ejecutar las migraciones.
                        </p>
                        @if(Route::has('hackathons.create'))
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i> Para comenzar a usar la funcionalidad de hackathones, ejecuta:
                            <br>
                            <code>php artisan hackathon:install</code>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('hackathons.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Crear mi primer hackathon
                            </a>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <strong>Nota:</strong> Es posible que necesites ejecutar las migraciones para habilitar la funcionalidad de hackathones.
                            <br>
                            <code>php artisan hackathon:install</code>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Entregables pendientes de corregir -->
        <div class="col-md-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title fw-semibold h5 mb-0">Entregables Pendientes de Evaluación</h4>
                    @php
                        $pendingDeliverables = [];
                        $totalPendingDeliverables = 0;
                        try {
                            if (Schema::hasTable('hackathon_deliverables') && Schema::hasTable('hackathons')) {
                                // Obtener hackathones creados por el profesor o donde es juez
                                $hackathonIds = App\Models\Hackathon::where('created_by', auth()->id())
                                    ->orWhereHas('judges', function($query) {
                                        $query->where('user_id', auth()->id());
                                    })
                                    ->pluck('id');
                                
                                // Obtener entregables pendientes con información relacionada
                                $pendingDeliverables = App\Models\HackathonDeliverable::whereHas('team', function($query) use ($hackathonIds) {
                                    $query->whereIn('hackathon_id', $hackathonIds);
                                })
                                ->with(['team.hackathon', 'round', 'user'])
                                ->whereNull('evaluated_at')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                                
                                // Contar total de entregables pendientes
                                $totalPendingDeliverables = App\Models\HackathonDeliverable::whereHas('team', function($query) use ($hackathonIds) {
                                    $query->whereIn('hackathon_id', $hackathonIds);
                                })
                                ->whereNull('evaluated_at')
                                ->count();
                            }
                        } catch (\Throwable $e) {
                            // No hacer nada si hay error
                        }
                    @endphp
                    @if($totalPendingDeliverables > 0)
                    <span class="badge bg-light text-danger">{{ $totalPendingDeliverables }} pendientes</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if(count($pendingDeliverables) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($pendingDeliverables as $deliverable)
                                <div class="list-group-item p-3">
                                    <div class="d-flex w-100 justify-content-between mb-1">
                                        <h6 class="mb-1 text-truncate">{{ $deliverable->title }}</h6>
                                        <small class="text-muted">{{ $deliverable->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1 small text-muted">
                                                <strong>Hackathon:</strong> {{ $deliverable->team->hackathon->title }} |
                                                <strong>Equipo:</strong> {{ $deliverable->team->name }} |
                                                <strong>Ronda:</strong> {{ $deliverable->round->title ?? 'Ronda ' . $deliverable->round->round_number }}
                                            </p>
                                            <p class="mb-0 small">
                                                <strong>Autor:</strong> {{ $deliverable->user->first_name }} {{ $deliverable->user->last_name }}
                                            </p>
                                        </div>
                                        <a href="{{ route('hackathons.deliverables.evaluate.round', ['hackathonId' => $deliverable->team->hackathon->id, 'roundId' => $deliverable->round_id]) }}" 
                                           class="btn btn-sm btn-danger">
                                            <i class="fas fa-star me-1"></i> Evaluar
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($totalPendingDeliverables > count($pendingDeliverables))
                            <div class="card-footer text-center py-2">
                                <a href="{{ route('teacher.hackathons') }}" class="text-danger">
                                    Ver todos los {{ $totalPendingDeliverables }} entregables pendientes
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="p-4 text-center">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p class="mb-0">No tienes entregables pendientes de evaluación.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @else
        <!-- Se ha eliminado la sección de "Actividad Reciente" -->
        @endif
    </div>

    @if(auth()->user()->role === 'teacher' && Route::has('hackathons.create'))
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="card-title fw-semibold h5 mb-0">Acciones rápidas para hackathones</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-grid">
                                <a href="{{ route('hackathons.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Crear nuevo hackathon
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid">
                                <a href="{{ route('hackathons.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-trophy me-1"></i> Ver todos los hackathones
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid">
                                <a href="{{ route('hackathons.index') }}?filter=active" class="btn btn-outline-success">
                                    <i class="fas fa-check-circle me-1"></i> Ver hackathones activos
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $isJudgeOfAnyHackathon = false;
                        $hackathonsAsJudge = collect([]);
                        try {
                            if (Schema::hasTable('hackathon_judges')) {
                                $judgeCount = DB::table('hackathon_judges')
                                    ->where('user_id', auth()->id())
                                    ->count();
                                $isJudgeOfAnyHackathon = $judgeCount > 0;
                                
                                if ($isJudgeOfAnyHackathon) {
                                    $hackathonsAsJudge = App\Models\Hackathon::whereHas('judges', function($query) {
                                        $query->where('user_id', auth()->id());
                                    })->where('status', 'active')
                                      ->orderBy('created_at', 'desc')
                                      ->take(3)
                                      ->get();
                                }
                            }
                        } catch (\Throwable $e) {
                            // No hacer nada si hay error
                        }
                    @endphp
                    
                    @if($isJudgeOfAnyHackathon && $hackathonsAsJudge->count() > 0)
                    <hr class="my-4">
                    <h5 class="fw-semibold mb-3">Hackathones donde soy juez</h5>
                    <div class="row">
                        @foreach($hackathonsAsJudge as $judgeHackathon)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-danger">
                                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $judgeHackathon->title }}</h6>
                                    @php
                                        $pendingCount = $judgeHackathon->pendingDeliverablesCount();
                                    @endphp
                                    @if($pendingCount > 0)
                                    <span class="badge bg-light text-danger">{{ $pendingCount }} pendientes</span>
                                    @else
                                    <span class="badge bg-light text-success">Al día</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <p class="card-text small">
                                        @if($judgeHackathon->current_round)
                                        <strong>Ronda actual:</strong> {{ $judgeHackathon->current_round }}
                                        <br>
                                        @endif
                                        <strong>Equipos:</strong> {{ $judgeHackathon->teams()->count() }}
                                    </p>
                                    <div class="d-grid">
                                        <a href="{{ route('hackathons.deliverables.evaluate', $judgeHackathon->hackathon_id) }}" class="btn btn-outline-danger">
                                            <i class="fas fa-clipboard-check me-1"></i> Evaluar entregables
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Sección de Desafíos para profesores -->
    @if(auth()->user()->role === 'teacher')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title fw-semibold h5 mb-0">Mis Desafíos</h4>
                    <a href="{{ route('challenges.create') }}" class="btn btn-sm btn-primary">Crear Nuevo Desafío</a>
                </div>
                
                @php
                    $challenges = \App\Models\TeachingChallenge::where('teacher_id', auth()->user()->user_id)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                @endphp
                
                @if($challenges->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Clase</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($challenges as $challenge)
                                <tr>
                                    <td>{{ $challenge->title }}</td>
                                    <td>
                                        @if($challenge->challenge_type == 'python')
                                            <span class="badge bg-primary">Python</span>
                                        @else
                                            <span class="badge bg-success">Prompts IA</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($challenge->status == 'published')
                                            <span class="badge bg-success">Publicado</span>
                                        @elseif($challenge->status == 'draft')
                                            <span class="badge bg-secondary">Borrador</span>
                                        @else
                                            <span class="badge bg-warning">Archivado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($challenge->class_id && $challenge->classroom)
                                            {{ $challenge->classroom->class_name }}
                                        @else
                                            <span class="badge bg-info">Público</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('challenges.edit', $challenge->id) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                        <a href="{{ route('challenges.preview', $challenge->id) }}" class="btn btn-sm btn-outline-info">Vista Previa</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 text-end">
                        <a href="{{ route('challenges.index') }}" class="btn btn-link">Ver Todos los Desafíos</a>
                    </div>
                @else
                    <div class="alert alert-info">
                        No has creado ningún desafío aún. ¡Comienza creando uno ahora!
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    <!-- Sección de Desafíos para estudiantes -->
    @if(auth()->user()->role === 'student')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title fw-semibold h5 mb-3">Mis Desafíos</h4>
                
                @php
                    $enrolledClassIds = DB::table('class_enrollments')
                        ->where('student_id', auth()->user()->user_id)
                        ->where('is_active', true)
                        ->pluck('class_id');
                        
                    $challenges = \App\Models\TeachingChallenge::whereIn('class_id', $enrolledClassIds)
                        ->orWhere('is_public', true)
                        ->where('status', 'published')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                
                @if($challenges->count() > 0)
                    <div class="row g-3">
                        @foreach($challenges as $challenge)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title h6 mb-0">{{ $challenge->title }}</h5>
                                        <span class="badge bg-{{ $challenge->challenge_type == 'python' ? 'primary' : 'success' }}">
                                            {{ $challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
                                        </span>
                                    </div>
                                    <p class="card-text text-muted small mb-3">{{ Str::limit($challenge->description, 80) }}</p>
                                    
                                    <!-- La barra de progreso ha sido eliminada para evitar errores -->
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-star text-warning me-1"></i> {{ $challenge->points }} pts
                                        </small>
                                        <a href="{{ route('student.challenges.show', $challenge->id) }}" class="btn btn-sm btn-outline-primary">Ver Desafío</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3 text-end">
                        <a href="{{ route('student.challenges.index') }}" class="btn btn-link">Ver Todos los Desafíos</a>
                    </div>
                @else
                    <div class="alert alert-info">
                        No hay desafíos disponibles actualmente. Consulta con tu profesor.
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if(auth()->user()->role === 'admin')
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Administración del Sitio</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="admin-card p-3 border rounded mb-3">
                        <h5><i class="fas fa-cog text-primary me-2"></i> Configuración del Sitio</h5>
                        <p class="text-muted">Administra el título, subtítulo, logo y botones del hero de la página principal.</p>
                        <a href="{{ route('admin.site-settings.edit') }}" class="btn btn-sm btn-primary">Configurar</a>
                    </div>
                </div>
                <!-- Otros enlaces de administración pueden ir aquí -->
            </div>
        </div>
    </div>
    @endif
@endsection 