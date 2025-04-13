@extends('layouts.app')

@section('title', $challenge->title)

@section('styles')
<style>
    /* Estilos para el modal de celebración */
    .celebration-modal .modal-content {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }
    
    .celebration-modal .modal-header {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .celebration-modal .modal-body {
        padding: 20px 30px 30px;
        text-align: center;
        color: white;
    }
    
    .celebration-modal h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        background: linear-gradient(to right, #fff, #f9ca24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .celebration-modal p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
    }
    
    .celebration-modal .btn-continue {
        background: #f9ca24;
        border: none;
        color: #222;
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 30px;
        font-size: 1.1rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    
    .celebration-modal .btn-continue:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }
    
    .celebration-modal .stats {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
    }
    
    .celebration-modal .stats-item {
        margin-bottom: 15px;
    }
    
    .celebration-modal .stats-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }
    
    .celebration-modal .stats-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        background-color: #f9ca24;
        opacity: 0.8;
        border-radius: 50%;
        animation: confetti-fall 5s ease-out forwards;
    }
    
    @keyframes confetti-fall {
        0% {
            transform: translateY(-100vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
    
    .trophy-animation {
        animation: trophy-bounce 0.8s ease-in-out infinite alternate;
    }
    
    @keyframes trophy-bounce {
        0% {
            transform: translateY(0);
        }
        100% {
            transform: translateY(-10px);
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.challenges.index') }}">Desafíos</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $challenge->title }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h2">{{ $challenge->title }}</h1>
            <span class="badge bg-{{ $challenge->challenge_type == 'python' ? 'primary' : 'success' }} py-2 px-3">
                {{ $challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
            </span>
        </div>
        
        <div class="badge bg-{{ $challenge->difficulty == 'principiante' ? 'success' : ($challenge->difficulty == 'intermedio' ? 'primary' : 'danger') }} mb-2">
            {{ ucfirst($challenge->difficulty) }}
        </div>
        
        @if($progress->is_completed)
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i> ¡Felicidades! Has completado este desafío y has ganado {{ $challenge->points }} puntos.
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Descripción</h5>
                    <p>{{ $challenge->description }}</p>
                    
                    <h5 class="card-title mt-4">Objetivos</h5>
                    <p>{{ $challenge->objectives }}</p>
                    
                    <h5 class="card-title mt-4">Instrucciones</h5>
                    <p>{{ $challenge->instructions }}</p>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ejercicios</h5>
                    <span class="badge bg-{{ $progress->is_completed ? 'success' : 'primary' }}">
                        {{ $progress->completed_exercises }}/{{ $progress->total_exercises }} completados
                    </span>
                </div>
                <div class="card-body">
                    <div class="progress mb-4" style="height: 20px;">
                        <div class="progress-bar {{ $progress->is_completed ? 'bg-success' : 'bg-primary' }}" 
                            role="progressbar" 
                            style="width: {{ $progress->total_exercises > 0 ? ($progress->completed_exercises / $progress->total_exercises) * 100 : 0 }}%" 
                            aria-valuenow="{{ $progress->total_exercises > 0 ? ($progress->completed_exercises / $progress->total_exercises) * 100 : 0 }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                            {{ $progress->total_exercises > 0 ? ($progress->completed_exercises / $progress->total_exercises) * 100 : 0 }}%
                        </div>
                    </div>
                
                    @if($challenge->exercises->isEmpty())
                        <div class="alert alert-info">
                            Este desafío aún no tiene ejercicios.
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($challenge->exercises as $exercise)
                                @php
                                    $isCompleted = isset($submissions[$exercise->id]) && $submissions[$exercise->id] && $submissions[$exercise->id]->status === 'graded' && $submissions[$exercise->id]->score > 0;
                                    $hasSubmission = isset($submissions[$exercise->id]) && $submissions[$exercise->id];
                                @endphp
                                <a href="{{ route('student.challenges.exercise', ['challengeId' => $challenge->id, 'exerciseId' => $exercise->id]) }}" 
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            @if($isCompleted)
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                            @elseif($hasSubmission)
                                                <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                            @else
                                                <i class="far fa-circle text-muted me-2"></i>
                                            @endif
                                            <h6 class="mb-0">{{ $exercise->title }}</h6>
                                        </div>
                                        @if($hasSubmission)
                                            <small class="text-muted ms-4">Último intento: {{ $submissions[$exercise->id]->created_at->format('d/m/Y H:i') }}</small>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @if($hasSubmission)
                                            <span class="badge bg-{{ $isCompleted ? 'success' : 'warning' }} me-2">
                                                {{ $submissions[$exercise->id]->score }}/100
                                            </span>
                                        @endif
                                        <span class="badge bg-primary">{{ $exercise->points }} pts</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Información del Desafío</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-clock me-2"></i> Tiempo estimado:</span>
                            <span class="fw-bold">{{ $challenge->estimated_time ? $challenge->estimated_time . ' min' : 'No especificado' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-trophy me-2"></i> Puntos:</span>
                            <span class="fw-bold">{{ $challenge->points }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-tasks me-2"></i> Ejercicios:</span>
                            <span class="fw-bold">{{ $challenge->exercises->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-graduation-cap me-2"></i> Clase:</span>
                            <span class="fw-bold">{{ $challenge->classroom && $challenge->classroom->class_name ? $challenge->classroom->class_name : 'Público' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Tu Progreso</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-calendar-check me-2"></i> Estado:</span>
                            <span class="badge bg-{{ $progress->is_completed ? 'success' : ($progress->is_in_progress ? 'warning' : 'secondary') }}">
                                {{ $progress->is_completed ? 'Completado' : ($progress->is_in_progress ? 'En Progreso' : 'No Iniciado') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-check me-2"></i> Ejercicios completados:</span>
                            <span class="fw-bold">{{ $progress->completed_exercises }}/{{ $progress->total_exercises }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><i class="fas fa-percentage me-2"></i> Porcentaje completado:</span>
                            <span class="fw-bold">{{ $progress->total_exercises > 0 ? ($progress->completed_exercises / $progress->total_exercises) * 100 : 0 }}%</span>
                        </li>
                        @if($progress->started_at)
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-play me-2"></i> Iniciado:</span>
                                <span class="fw-bold">{{ $progress->started_at->format('d/m/Y H:i') }}</span>
                            </li>
                        @endif
                        @if($progress->completed_at)
                            <li class="list-group-item d-flex justify-content-between">
                                <span><i class="fas fa-flag-checkered me-2"></i> Completado:</span>
                                <span class="fw-bold">{{ $progress->completed_at->format('d/m/Y H:i') }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="d-grid">
                <a href="{{ route('student.challenges.index') }}" class="btn btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left me-1"></i> Volver a la lista
                </a>
                
                @if(!$challenge->exercises->isEmpty())
                    @php
                        $firstIncompleteExercise = null;
                        foreach($challenge->exercises as $ex) {
                            $isCompleted = isset($submissions[$ex->id]) && $submissions[$ex->id] && $submissions[$ex->id]->status === 'graded' && $submissions[$ex->id]->score > 0;
                            if(!$isCompleted) {
                                $firstIncompleteExercise = $ex;
                                break;
                            }
                        }
                        
                        if(!$firstIncompleteExercise && $challenge->exercises->isNotEmpty()) {
                            $firstIncompleteExercise = $challenge->exercises->first();
                        }
                    @endphp
                    
                    @if($firstIncompleteExercise)
                        <a href="{{ route('student.challenges.exercise', ['challengeId' => $challenge->id, 'exerciseId' => $firstIncompleteExercise->id]) }}" 
                            class="btn btn-primary">
                            @if($progress->completed_exercises > 0)
                                <i class="fas fa-play me-1"></i> Continuar
                            @else
                                <i class="fas fa-play me-1"></i> Comenzar Desafío
                            @endif
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Celebración -->
<div class="modal fade celebration-modal" id="celebrationModal" tabindex="-1" aria-labelledby="celebrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <i class="fas fa-trophy fa-4x text-warning trophy-animation"></i>
                </div>
                
                <h2>¡DESAFÍO COMPLETADO!</h2>
                <p>¡Felicidades! Has completado con éxito el desafío "<strong>{{ $challenge->title }}</strong>"</p>
                
                <div class="stats row">
                    <div class="col-md-4 stats-item">
                        <div class="stats-label">Puntos ganados</div>
                        <div class="stats-value">+{{ $challenge->points }}</div>
                    </div>
                    <div class="col-md-4 stats-item">
                        <div class="stats-label">Ejercicios completados</div>
                        <div class="stats-value">{{ $progress->total_exercises }}/{{ $progress->total_exercises }}</div>
                    </div>
                    <div class="col-md-4 stats-item">
                        <div class="stats-label">Tiempo empleado</div>
                        <div class="stats-value">
                            @if($progress->completed_at && $progress->started_at)
                                @php
                                    $interval = $progress->started_at->diff($progress->completed_at);
                                    $hours = str_pad($interval->h + ($interval->days * 24), 2, '0', STR_PAD_LEFT);
                                    $minutes = str_pad($interval->i, 2, '0', STR_PAD_LEFT);
                                    $seconds = str_pad($interval->s, 2, '0', STR_PAD_LEFT);
                                    $formattedTime = "{$hours}:{$minutes}:{$seconds}";
                                @endphp
                                {{ $formattedTime }}
                            @else
                                --
                            @endif
                        </div>
                    </div>
                </div>
                
                <p>¡Sigue así! Cada desafío que completas te acerca más a dominar la inteligencia artificial.</p>
                
                <button type="button" class="btn btn-continue" data-bs-dismiss="modal">
                    ¡Seguir aprendiendo!
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Script para manejar la celebración -->
<script>
    // Crear sonido de celebración desde base64 (como no podemos descargar el archivo)
    function createAchievementSound() {
        // Creamos el elemento de audio
        const completionSound = new Audio();
        
        // Usamos una breve secuencia de notas generada con AudioContext
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Función para reproducir una nota
            const playNote = (frequency, time, duration) => {
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.type = 'sine';
                oscillator.frequency.value = frequency;
                gainNode.gain.value = 0.5;
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.start(audioContext.currentTime + time);
                oscillator.stop(audioContext.currentTime + time + duration);
                
                // Fade out
                gainNode.gain.setValueAtTime(0.5, audioContext.currentTime + time);
                gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + time + duration);
            };
            
            // Tocar una secuencia de notas alegres
            if (@json($progress->is_completed && session('challenge_just_completed'))) {
                // Melodía de celebración (escala mayor ascendente)
                setTimeout(() => {
                    playNote(523.25, 0, 0.2);  // C5
                    playNote(587.33, 0.2, 0.2); // D5
                    playNote(659.26, 0.4, 0.2); // E5
                    playNote(698.46, 0.6, 0.2); // F5
                    playNote(783.99, 0.8, 0.2); // G5
                    playNote(880.00, 1.0, 0.2); // A5
                    playNote(987.77, 1.2, 0.2); // B5
                    playNote(1046.50, 1.4, 0.4); // C6 (más largo)
                }, 500);
            }
            
        } catch (e) {
            console.error("Error al crear sonido de celebración", e);
        }
        
        return completionSound;
    }
    
    // Función para crear confeti
    function createConfetti() {
        const confettiColors = ['#f9ca24', '#6c5ce7', '#a29bfe', '#fd79a8', '#00cec9'];
        const confettiContainer = document.querySelector('.celebration-modal .modal-content');
        
        for (let i = 0; i < 150; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.animationDelay = Math.random() * 2 + 's';
            confetti.style.backgroundColor = confettiColors[Math.floor(Math.random() * confettiColors.length)];
            confetti.style.width = Math.random() * 10 + 5 + 'px';
            confetti.style.height = confetti.style.width;
            
            confettiContainer.appendChild(confetti);
            
            // Eliminar el confeti después de la animación
            setTimeout(() => {
                confetti.remove();
            }, 5000);
        }
    }
    
    // Mostrar modal de celebración si el desafío fue completado
    document.addEventListener('DOMContentLoaded', function() {
        @if($progress->is_completed && session('challenge_just_completed'))
            const celebrationModal = new bootstrap.Modal(document.getElementById('celebrationModal'));
            // Crear sonido
            createAchievementSound();
            
            setTimeout(() => {
                celebrationModal.show();
                createConfetti();
            }, 1000);
        @endif
    });
</script>
@endsection 