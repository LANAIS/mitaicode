@extends('layouts.app')

@section('title', 'Chat de Equipo - ' . $team->name)

@section('header', 'Chat de Equipo - ' . $team->name)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.hackathons.index') }}">Hackathones</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.hackathons.details', ['id' => $team->hackathon->id]) }}">{{ $team->hackathon->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.hackathons.team', ['id' => $team->team_id]) }}">{{ $team->team_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chat de equipo</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Información del equipo -->
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ $team->name }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">{{ $team->hackathon->title }}</p>
                
                <h6 class="fw-bold mb-2">Miembros del equipo</h6>
                <ul class="list-group mb-3">
                    @foreach($team->members as $member)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ $member->profile_image ?? asset('img/avatars/default.png') }}" class="rounded-circle me-2" width="25" height="25" alt="Avatar">
                            <span>{{ $member->name }} {{ $member->id == Auth::id() ? '(Tú)' : '' }}</span>
                        </div>
                        @if($member->id == $team->leader_id)
                        <span class="badge rounded-pill bg-primary">Líder</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('student.hackathons.team.deliverables', $team->team_id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-upload me-1"></i> Entregables
                    </a>
                    @if($team->repository_url)
                    <a href="{{ route('student.hackathons.team.repository', $team->team_id) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-code-branch me-1"></i> Repositorio
                    </a>
                    @else
                    <a href="{{ route('student.hackathons.team.edit', $team->team_id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-link me-1"></i> Añadir repositorio
                    </a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Fase Actual</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Ronda:</strong> {{ $team->hackathon->current_round_name ?? 'No disponible' }}</p>
                <p class="mb-2"><strong>Fecha límite:</strong> {{ $team->hackathon->current_round_end_date ? \Carbon\Carbon::parse($team->hackathon->current_round_end_date)->format('d/m/Y') : 'No disponible' }}</p>
                
                <div class="progress mt-3" style="height: 8px;">
                    @php
                        $totalRounds = 3; // Por defecto
                        $currentRound = $team->hackathon->current_round ?? 1;
                        $progress = ($currentRound / $totalRounds) * 100;
                    @endphp
                    <div class="progress-bar progress-bar-striped progress-bar-animated 
                        {{ $currentRound > 1 ? 'bg-success' : '' }}" 
                        role="progressbar" 
                        style="width: {{ $progress }}%" 
                        aria-valuenow="{{ $progress }}" 
                        aria-valuemin="0" 
                        aria-valuemax="100"></div>
                </div>
                <small class="text-muted">Ronda {{ $currentRound }} de {{ $totalRounds }}</small>
            </div>
        </div>
    </div>
    
    <!-- Chat principal -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3>{{ $team->name }} - Chat de equipo</h3>
                <div class="d-flex">
                    <a href="{{ route('student.hackathons.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Volver a Hackathons
                    </a>
                    <a href="{{ route('student.hackathons.team', ['id' => $team->id]) }}" class="btn btn-primary me-2">
                        <i class="fas fa-users"></i> Equipo
                    </a>
                    <a href="{{ route('student.hackathons.team.repository', ['id' => $team->id]) }}" class="btn btn-info me-2">
                        <i class="fab fa-github"></i> Repositorio
                    </a>
                    <a href="{{ route('student.hackathons.team.edit', ['id' => $team->id]) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Editar equipo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Mensajes del chat -->
                <div class="chat-container mb-3" id="chat-messages" style="height: 400px; overflow-y: auto;">
                    @forelse($messages as $message)
                        <div class="chat-message {{ $message->user_id == Auth::id() ? 'user-message' : 'ai-message' }} mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <img src="{{ $message->user->profile_image ?? asset('img/avatars/default.png') }}" alt="Avatar" class="rounded-circle" width="40" height="40">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold">{{ $message->user->name }}</span>
                                        <small class="text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="chat-bubble p-2 rounded">
                                        {!! nl2br(e($message->message)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p>No hay mensajes en el chat todavía. ¡Sé el primero en enviar uno!</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Formulario para enviar mensajes -->
                <form id="chat-form" class="chat-form">
                    @csrf
                    <div class="input-group">
                        <textarea class="form-control" id="message-input" placeholder="Escribe tu mensaje..." rows="2" required></textarea>
                        <button class="btn btn-primary" type="submit" id="send-button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@livewire('chat-component', ['teamId' => $team->id])

<div class="text-center mt-4">
    <a href="{{ route('student.hackathons.index') }}" class="btn btn-secondary me-2">
        <i class="fas fa-arrow-left me-1"></i> Volver a Hackathones
    </a>
    <a href="{{ route('student.hackathons.team.deliverables', ['id' => $team->id]) }}" class="btn btn-primary">
        <i class="fas fa-file-upload me-1"></i> Entregables del equipo
    </a>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        
        // Scroll al final del chat
        chatContainer.scrollTop = chatContainer.scrollHeight;
        
        // Enviar mensaje
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;
            
            // Deshabilitar el botón mientras se envía
            const sendButton = document.getElementById('send-button');
            sendButton.disabled = true;
            
            // Enviar mensaje al servidor
            fetch('{{ route("student.hackathons.team.chat.send", ["id" => $team->id]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Añadir el mensaje al chat
                    addMessageToChat(data.message);
                    
                    // Limpiar el input
                    messageInput.value = '';
                    
                    // Scroll al final del chat
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                } else {
                    alert('Error al enviar el mensaje: ' + (data.error || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al enviar el mensaje. Por favor, inténtalo de nuevo.');
            })
            .finally(() => {
                sendButton.disabled = false;
            });
        });
        
        // Función para añadir un mensaje al chat
        function addMessageToChat(message) {
            const messageElement = document.createElement('div');
            messageElement.className = 'chat-message user-message mb-3';
            messageElement.innerHTML = `
                <div class="d-flex">
                    <div class="flex-shrink-0 me-2">
                        <img src="${message.user.avatar}" alt="Avatar" class="rounded-circle" width="40" height="40">
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold">${message.user.name}</span>
                            <small class="text-muted">${message.date} ${message.time}</small>
                        </div>
                        <div class="chat-bubble p-2 rounded">
                            ${message.text.replace(/\n/g, '<br>')}
                        </div>
                    </div>
                </div>
            `;
            
            chatContainer.appendChild(messageElement);
        }
        
        // Permitir enviar con Enter (pero nueva línea con Shift+Enter)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });
        
        // Setear altura automática al textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
</script>
@endsection 