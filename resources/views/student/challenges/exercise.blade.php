@extends('layouts.app')

@section('title', $exercise->title)

@section('styles')
<style>
    .code-editor {
        height: 400px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .exercise-instructions {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .hint-container {
        background-color: #fff8e1;
        border-left: 4px solid #ffc107;
    }

    .btn-submitting {
        position: relative;
        pointer-events: none;
    }
    
    .btn-submitting .spinner-border {
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
    }
    
    /* Estilos para el terminal */
    #terminal {
        background-color: #121212;
        color: #f0f0f0;
        font-family: 'Cascadia Code', 'Fira Code', 'Consolas', 'Monaco', 'Courier New', monospace;
        padding: 15px;
        border-radius: 8px;
        height: 300px;
        overflow-y: auto;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5);
        position: relative;
    }
    
    #terminal p {
        margin: 0;
        padding: 2px 0;
        line-height: 1.4;
        white-space: pre-wrap;
        word-break: break-word;
    }
    
    #terminal p:last-child::after {
        content: "_";
        animation: blink 1s step-end infinite;
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
    
    /* Output types */
    #terminal .output-string {
        color: #4caf50;
    }
    
    #terminal .output-number {
        color: #2196f3;
    }
    
    #terminal .output-error {
        color: #f44336;
        font-weight: bold;
    }
    
    #terminal .output-warning {
        color: #ff9800;
    }
    
    #terminal .output-processing {
        color: #9c27b0;
        font-style: italic;
    }
    
    #terminal .output-prompt {
        color: #03a9f4;
        font-weight: bold;
    }
    
    /* Terminal header bar */
    .terminal-header {
        background: #2c3e50;
        color: white;
        padding: 8px 15px;
        border-radius: 8px 8px 0 0;
        font-size: 14px;
        font-family: system-ui, -apple-system, sans-serif;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .terminal-title {
        display: flex;
        align-items: center;
    }
    
    .terminal-title i {
        margin-right: 8px;
    }
    
    .terminal-controls {
        display: flex;
    }
    
    .terminal-button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-left: 8px;
        cursor: pointer;
    }
    
    .terminal-button.close {
        background-color: #ff5f56;
    }
    
    .terminal-button.minimize {
        background-color: #ffbd2e;
    }
    
    .terminal-button.maximize {
        background-color: #27c93f;
    }
    
    /* Estilos para las pestañas */
    .code-tabs .nav-link {
        color: #495057;
        background-color: #f8f9fa;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    
    .code-tabs .nav-link.active {
        color: #007bff;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: bold;
    }
    
    /* Estilos para la sección del tutor IA */
    .ai-tutor-container {
        max-height: 300px;
        overflow-y: auto;
        background-color: #f8f9fc;
        border-radius: 8px;
        padding: 12px;
        box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }
    
    .ai-message {
        background-color: #e6f3ff;
        border-left: 3px solid #1e88e5;
        padding: 12px;
        margin-bottom: 12px;
        border-radius: 0 8px 8px 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    
    .ai-message:before {
        content: "";
        position: absolute;
        left: -10px;
        top: 10px;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid #e6f3ff;
    }
    
    .ai-message .ai-avatar {
        position: absolute;
        left: -32px;
        top: 12px;
        width: 24px;
        height: 24px;
        background-color: #1e88e5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
    }
    
    .user-message {
        background-color: #f0f0f0;
        border-left: 3px solid #666;
        padding: 12px;
        margin-bottom: 12px;
        border-radius: 0 8px 8px 0;
        margin-left: 15px;
        position: relative;
    }
    
    .user-message:before {
        content: "";
        position: absolute;
        left: -10px;
        top: 10px;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid #f0f0f0;
    }
    
    .quick-question-btn {
        transition: all 0.2s ease;
        margin-bottom: 4px;
    }
    
    .quick-question-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    /* Estilos para el input del tutor IA */
    .tutor-input-container {
        position: relative;
        margin-top: 15px;
    }
    
    .tutor-input-container .form-control {
        border-radius: 20px;
        padding-left: 15px;
        padding-right: 50px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }
    
    .tutor-input-container .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-color: #86b7fe;
    }
    
    .tutor-input-container .btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    /* Badges para categorías de preguntas */
    .question-category {
        background-color: #e9ecef;
        color: #495057;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 11px;
        margin-right: 5px;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.challenges.index') }}">Desafíos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('student.challenges.show', $challenge->id) }}">{{ $challenge->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $exercise->title }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h2">{{ $exercise->title }}</h1>
            <span class="badge bg-{{ $challenge->challenge_type == 'python' ? 'primary' : 'success' }} py-2 px-3">
                {{ $challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}
            </span>
        </div>
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
        <!-- Panel izquierdo - Instrucciones y AI Tutor -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Instrucciones</h5>
                </div>
                <div class="card-body exercise-instructions">
                    @if($exercise->description)
                        <h6>Descripción</h6>
                        <p>{{ $exercise->description }}</p>
                    @endif
                    
                    <h6 class="mt-3">¿Qué hacer?</h6>
                    <p>{{ $exercise->instructions }}</p>
                    
                    @if($hint)
                        <div class="hint-container p-3 mt-3">
                            <h6 class="text-warning"><i class="fas fa-lightbulb me-2"></i> Pista</h6>
                            <p class="mb-0">{{ $hint }}</p>
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <h6>Intentos</h6>
                        <p>Este es tu intento #{{ $attemptNumber }}</p>
                        
                        @if($submissions->isNotEmpty())
                            <h6 class="mt-3">Entregas anteriores</h6>
                            <div class="list-group">
                                @foreach($submissions as $submission)
                                    <a href="{{ route('student.challenges.submission', $submission->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-{{ $submission->status == 'graded' && $submission->score > 70 ? 'success' : ($submission->status == 'graded' ? 'warning' : 'secondary') }} me-2">
                                                {{ $submission->status == 'graded' ? $submission->score : 'Pendiente' }}
                                            </span>
                                            <small>{{ $submission->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Tutor IA -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-robot me-2"></i> Tutor IA</h5>
                    <span class="badge bg-light text-primary">24/7</span>
                </div>
                <div class="card-body">
                    <div class="ai-tutor-container" id="aiTutorChat">
                        <div class="ai-message">
                            <div class="ai-avatar">
                                <i class="fas fa-robot fa-xs"></i>
                            </div>
                            <span class="question-category">Inicio</span>
                            <p>¡Hola! Soy tu tutor de IA para este ejercicio de {{ $challenge->challenge_type == 'python' ? 'Python' : 'Prompts IA' }}. Estoy aquí para ayudarte a resolver tus dudas.</p>
                            <p class="mb-0">Puedes preguntarme sobre conceptos, sintaxis o cómo abordar este ejercicio. ¡También puedes usar los botones de abajo para preguntas rápidas!</p>
                        </div>
                    </div>
                    
                    <!-- Categorías y preguntas frecuentes -->
                    <div class="mb-3">
                        <h6 class="text-muted mb-2"><small>PREGUNTAS POPULARES</small></h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary quick-question-btn" data-question="¿Cómo empiezo este ejercicio?">
                                <i class="fas fa-play-circle me-1"></i> ¿Cómo empiezo?
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-question-btn" data-question="Dame una pista para resolver este ejercicio">
                                <i class="fas fa-lightbulb me-1"></i> Pista
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-question-btn" data-question="¿Qué conceptos necesito entender para este ejercicio?">
                                <i class="fas fa-book me-1"></i> Conceptos clave
                            </button>
                            @if($challenge->challenge_type == 'python')
                            <button type="button" class="btn btn-sm btn-outline-primary quick-question-btn" data-question="¿Cómo se hace un bucle for en Python?">
                                <i class="fas fa-sync me-1"></i> Bucle for
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-question-btn" data-question="¿Cómo funcionan las funciones en Python?">
                                <i class="fas fa-code me-1"></i> Funciones
                            </button>
                            @else
                            <button type="button" class="btn btn-sm btn-outline-primary quick-question-btn" data-question="¿Cómo escribo un buen prompt?">
                                <i class="fas fa-pencil-alt me-1"></i> Escribir prompts
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-question-btn" data-question="¿Qué buenas prácticas debo seguir para este ejercicio de IA?">
                                <i class="fas fa-check-double me-1"></i> Buenas prácticas
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Input de preguntas mejorado -->
                    <div class="tutor-input-container">
                        <input type="text" class="form-control" id="aiTutorQuestion" placeholder="Escribe tu pregunta aquí...">
                        <button class="btn btn-primary" type="button" id="askTutorBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel derecho - Editor de código o prompt con mejoras -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs card-header-tabs code-tabs" id="codeEditorTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="editor-tab" data-bs-toggle="tab" data-bs-target="#editor" type="button" role="tab" aria-controls="editor" aria-selected="true">
                                <i class="fas fa-code me-1"></i> Editor
                            </button>
                        </li>
                        @if($challenge->challenge_type == 'python')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="terminal-tab" data-bs-toggle="tab" data-bs-target="#terminalTab" type="button" role="tab" aria-controls="terminalTab" aria-selected="false">
                                <i class="fas fa-terminal me-1"></i> Terminal
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ai-corrector-tab" data-bs-toggle="tab" data-bs-target="#aiCorrector" type="button" role="tab" aria-controls="aiCorrector" aria-selected="false">
                                <i class="fas fa-check-circle me-1"></i> Corrector IA
                            </button>
                        </li>
                        @endif
                    </ul>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="fullscreenBtn">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="codeEditorTabsContent">
                        <!-- Pestaña del Editor de Código -->
                        <div class="tab-pane fade show active" id="editor" role="tabpanel" aria-labelledby="editor-tab">
                            <form action="{{ route('student.challenges.submit', $exercise->id) }}" method="POST" id="exerciseForm">
                                @csrf
                                
                                @if($challenge->challenge_type == 'python')
                                    <div class="mb-3">
                                        <textarea class="form-control code-editor" id="code" name="code" rows="15" required>{{ $exercise->starter_code }}</textarea>
                                        @error('code')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <label for="prompt" class="form-label">Tu Prompt</label>
                                        <textarea class="form-control" id="prompt" name="prompt" rows="10" placeholder="Escribe tu prompt para la IA..." required>{{ old('prompt') }}</textarea>
                                        @error('prompt')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    @if($exercise->example_prompt)
                                        <div class="mb-3">
                                            <label class="form-label">Ejemplo de Prompt</label>
                                            <div class="border p-3 bg-light">
                                                <pre class="mb-0">{{ $exercise->example_prompt }}</pre>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <div>
                                        <a href="{{ route('student.challenges.show', $challenge->id) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Volver al desafío
                                        </a>
                                        @if($challenge->challenge_type == 'python')
                                        <button type="button" class="btn btn-success ms-2" id="runCodeBtn">
                                            <i class="fas fa-play me-1"></i> Ejecutar código
                                        </button>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-paper-plane me-1"></i> Enviar solución
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Pestaña de Terminal (solo visible para desafíos de Python) -->
                        @if($challenge->challenge_type == 'python')
                        <div class="tab-pane fade" id="terminalTab" role="tabpanel" aria-labelledby="terminal-tab">
                            <div class="mb-3">
                                <div class="terminal-header">
                                    <div class="terminal-title">
                                        <i class="fas fa-terminal"></i>
                                        <span>Python Terminal</span>
                                    </div>
                                    <div class="terminal-controls">
                                        <div class="terminal-button minimize"></div>
                                        <div class="terminal-button maximize"></div>
                                        <div class="terminal-button close"></div>
                                    </div>
                                </div>
                                <div id="terminal">
                                    <p class="output-prompt">Terminal Python lista para ejecutar tu código...</p>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" type="button" id="runCodeFromTerminalBtn">
                                    <i class="fas fa-play me-1"></i> Ejecutar código
                                </button>
                                <button class="btn btn-outline-secondary" type="button" id="clearTerminalBtn">
                                    <i class="fas fa-eraser me-1"></i> Limpiar terminal
                                </button>
                            </div>
                        </div>
                        
                        <!-- Pestaña de Corrector IA (solo visible para desafíos de Python) -->
                        <div class="tab-pane fade" id="aiCorrector" role="tabpanel" aria-labelledby="ai-corrector-tab">
                            <div class="mb-3">
                                <label class="form-label">Revisión preliminar de tu código</label>
                                <div class="p-3 border rounded" id="aiCorrectorOutput">
                                    <p class="text-muted">Haz clic en "Verificar código" para obtener una evaluación preliminar de tu solución.</p>
                                </div>
                                <small class="text-muted d-block mt-2">Esta revisión no cuenta como un envío oficial y solo sirve como guía.</small>
                            </div>
                            <button class="btn btn-primary" type="button" id="checkCodeWithAIBtn">
                                <i class="fas fa-check-double me-1"></i> Verificar código
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if(!empty($previousSubmission))
                <div class="card shadow-sm mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Tu última entrega</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Código enviado</label>
                            <pre class="border p-3 bg-light">{{ $previousSubmission->submitted_code ?? $previousSubmission->submitted_prompt }}</pre>
                        </div>
                        
                        @if($previousSubmission->feedback)
                            <div class="mb-3">
                                <label class="form-label">Feedback</label>
                                <div class="border p-3">
                                    {{ $previousSubmission->feedback }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Expandir editor a pantalla completa
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const codeEditor = document.querySelector('.card-body');
        
        fullscreenBtn.addEventListener('click', function() {
            if (!document.fullscreenElement) {
                codeEditor.requestFullscreen().catch(err => {
                    console.error(`Error al intentar mostrar a pantalla completa: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        });
        
        // Añadir efecto de carga al enviar
        const exerciseForm = document.getElementById('exerciseForm');
        const submitBtn = document.getElementById('submitBtn');
        
        exerciseForm.addEventListener('submit', function(e) {
            // Mostrar indicador de carga
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Evaluando...';
            submitBtn.classList.add('btn-submitting');
            submitBtn.disabled = true;
            
            // Mostrar mensaje de espera
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-info mt-3';
            alertDiv.innerHTML = '<i class="fas fa-robot me-2"></i> Tu solución está siendo evaluada por la IA. Este proceso puede tardar unos segundos, por favor espera...';
            
            // Insertar mensaje después del botón
            exerciseForm.appendChild(alertDiv);
        });
        
        // Funcionalidad para el tutor IA
        const aiTutorQuestion = document.getElementById('aiTutorQuestion');
        const askTutorBtn = document.getElementById('askTutorBtn');
        const aiTutorChat = document.getElementById('aiTutorChat');
        let conversationId = null; // Para mantener el ID de conversación
        
        askTutorBtn.addEventListener('click', function() {
            const question = aiTutorQuestion.value.trim();
            if (!question) return;
            
            // Agregar mensaje del usuario
            const userMessageDiv = document.createElement('div');
            userMessageDiv.className = 'user-message';
            userMessageDiv.innerHTML = `<p>${question}</p>`;
            aiTutorChat.appendChild(userMessageDiv);
            
            // Limpiar campo de entrada
            aiTutorQuestion.value = '';
            
            // Mostrar indicador de carga
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'ai-message';
            loadingDiv.innerHTML = `
                <div class="ai-avatar">
                    <i class="fas fa-robot fa-xs"></i>
                </div>
                <p><i class="fas fa-spinner fa-spin"></i> Pensando...</p>
            `;
            aiTutorChat.appendChild(loadingDiv);
            aiTutorChat.scrollTop = aiTutorChat.scrollHeight;
            
            // Llamar a la API real
            fetch('/api/ai-tutor/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    question: question,
                    challenge_type: "{{ $challenge->challenge_type }}",
                    exercise_title: "{{ $exercise->title }}",
                    conversation_id: conversationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Guardar el ID de conversación para futuros mensajes
                    conversationId = data.conversation_id;
                    
                    // Reemplazar el mensaje de carga con la respuesta
                    loadingDiv.innerHTML = `
                        <div class="ai-avatar">
                            <i class="fas fa-robot fa-xs"></i>
                        </div>
                        <span class="question-category">${data.category}</span>
                        ${data.response}
                    `;
                } else {
                    // Mostrar mensaje de error
                    loadingDiv.innerHTML = `
                        <div class="ai-avatar">
                            <i class="fas fa-robot fa-xs"></i>
                        </div>
                        <span class="question-category">Error</span>
                        <p>Lo siento, ocurrió un error al procesar tu pregunta. Por favor, inténtalo de nuevo.</p>
                    `;
                }
                aiTutorChat.scrollTop = aiTutorChat.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                loadingDiv.innerHTML = `
                    <div class="ai-avatar">
                        <i class="fas fa-robot fa-xs"></i>
                    </div>
                    <span class="question-category">Error</span>
                    <p>Lo siento, no pude conectarme con el servidor. Por favor, verifica tu conexión e inténtalo de nuevo.</p>
                `;
                aiTutorChat.scrollTop = aiTutorChat.scrollHeight;
            });
        });
        
        // Determinar categoría de pregunta para mostrar tag visual
        function determinarCategoriaPregunta(pregunta) {
            const preguntaLower = pregunta.toLowerCase();
            
            if (preguntaLower.includes('empie') || preguntaLower.includes('empeza')) {
                return 'Inicio';
            } else if (preguntaLower.includes('pista') || preguntaLower.includes('ayuda') || preguntaLower.includes('hint')) {
                return 'Pista';
            } else if (preguntaLower.includes('bucle') || preguntaLower.includes('for') || preguntaLower.includes('while')) {
                return 'Bucles';
            } else if (preguntaLower.includes('funcion') || preguntaLower.includes('función') || preguntaLower.includes('def')) {
                return 'Funciones';
            } else if (preguntaLower.includes('error') || preguntaLower.includes('fallo') || preguntaLower.includes('problema')) {
                return 'Error';
            } else if (preguntaLower.includes('variable') || preguntaLower.includes('tipo') || preguntaLower.includes('dato')) {
                return 'Variables';
            } else if (preguntaLower.includes('lista') || preguntaLower.includes('array') || preguntaLower.includes('colección')) {
                return 'Estructuras';
            } else if (preguntaLower.includes('prompt') || preguntaLower.includes('ia') || preguntaLower.includes('inteligencia')) {
                return 'Prompts IA';
            } else if (preguntaLower.includes('concepto') || preguntaLower.includes('entender') || preguntaLower.includes('clave')) {
                return 'Conceptos';
            } else {
                return 'Consulta';
            }
        }
        
        // Evento para enviar la pregunta al presionar Enter
        aiTutorQuestion.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                askTutorBtn.click();
            }
        });
        
        // Botones de preguntas rápidas
        const quickQuestionBtns = document.querySelectorAll('.quick-question-btn');
        quickQuestionBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const question = this.getAttribute('data-question');
                aiTutorQuestion.value = question;
                askTutorBtn.click();
            });
        });
        
        // Funcionalidad para ejecutar código
        const runCodeBtn = document.getElementById('runCodeBtn');
        const runCodeFromTerminalBtn = document.getElementById('runCodeFromTerminalBtn');
        const clearTerminalBtn = document.getElementById('clearTerminalBtn');
        const terminal = document.getElementById('terminal');
        const codeTextarea = document.getElementById('code');
        
        function ejecutarCodigo() {
            const codigo = codeTextarea.value;
            if (!codigo.trim()) {
                mostrarEnTerminal("Error: No hay código para ejecutar", 'error');
                return;
            }
            
            mostrarEnTerminal("Ejecutando código...", 'processing');
            
            // En un entorno real, esto llamaría a tu API para ejecutar el código
            // Por ahora, simulamos la ejecución
            simulateCodeExecution(codigo);
        }
        
        function mostrarEnTerminal(texto, tipo = 'default') {
            // Agregar texto al terminal con formato según el tipo
            const p = document.createElement('p');
            
            // Determinar la clase CSS basada en el tipo de mensaje
            switch(tipo) {
                case 'string':
                    p.className = 'output-string';
                    break;
                case 'number':
                    p.className = 'output-number';
                    break;
                case 'error':
                    p.className = 'output-error';
                    break;
                case 'warning':
                    p.className = 'output-warning';
                    break;
                case 'processing':
                    p.className = 'output-processing';
                    break;
                case 'prompt':
                    p.className = 'output-prompt';
                    break;
                default:
                    // Sin clase especial
                    break;
            }
            
            // Detectar tipo de contenido en el texto para formato automático
            if (!tipo || tipo === 'default') {
                if (texto.startsWith('>>>')) {
                    // Valor de retorno de una operación
                    const outputValue = texto.substring(4);
                    if (!isNaN(outputValue) && outputValue.trim() !== '') {
                        p.className = 'output-number';
                    } else if (outputValue.includes("Error") || outputValue.includes("error")) {
                        p.className = 'output-error';
                    } else if (outputValue.includes("Advertencia") || outputValue.includes("advertencia")) {
                        p.className = 'output-warning';
                    } else {
                        p.className = 'output-string';
                    }
                } else if (texto.includes("Error") || texto.includes("error")) {
                    p.className = 'output-error';
                } else if (texto.includes("Ejecutando")) {
                    p.className = 'output-processing';
                }
            }
            
            p.textContent = texto;
            terminal.appendChild(p);
            terminal.scrollTop = terminal.scrollHeight;
        }
        
        function simulateCodeExecution(codigo) {
            // Esta es una simulación avanzada, en un entorno real
            // enviarías el código a un backend que lo ejecutaría en un entorno seguro
            
            // Limpiar cualquier error o mensaje previo
            let hasError = false;
            mostrarEnTerminal(">>> Ejecutando código Python...", 'processing');
            
            try {
                // Verificar la sintaxis básica de Python
                checkBasicSyntax(codigo);
                
                // Procesamiento de código Python
                processPythonCode(codigo);
                
                // Mensaje de finalización
                setTimeout(() => {
                    if (!hasError) {
                        mostrarEnTerminal("\n>>> Ejecución completada 🎉", 'prompt');
                    }
                }, 1000);
            } catch (error) {
                hasError = true;
                mostrarEnTerminal("\n>>> SyntaxError: " + error.message, 'error');
            }
        }
        
        function checkBasicSyntax(codigo) {
            // Revisa errores comunes de sintaxis
            
            // Verificar paréntesis equilibrados
            let parenthesisCount = 0;
            for (let char of codigo) {
                if (char === '(') parenthesisCount++;
                if (char === ')') parenthesisCount--;
                if (parenthesisCount < 0) throw new Error("Paréntesis desbalanceados ')'");
            }
            if (parenthesisCount > 0) throw new Error("Falta paréntesis de cierre ')'");
            
            // Verificar comillas sin cerrar
            const singleQuotes = codigo.match(/'/g) || [];
            const doubleQuotes = codigo.match(/"/g) || [];
            if (singleQuotes.length % 2 !== 0) throw new Error("Comillas simples sin cerrar (')");
            if (doubleQuotes.length % 2 !== 0) throw new Error("Comillas dobles sin cerrar (\")");
            
            // Verificar palabras clave de Python
            const pythonKeywords = ['def', 'if', 'else', 'elif', 'for', 'while', 'try', 'except', 'class', 'import', 'from', 'return'];
            const lines = codigo.split('\n');
            
            for (let i = 0; i < lines.length; i++) {
                const line = lines[i].trim();
                
                // Saltar líneas vacías o comentarios
                if (line === '' || line.startsWith('#')) continue;
                
                // Verificar dos puntos después de estructuras de control
                for (const keyword of pythonKeywords) {
                    if (line.startsWith(keyword + ' ') && !line.includes(':')) {
                        throw new Error(`Falta dos puntos ':' después de '${keyword}' en línea ${i+1}`);
                    }
                }
            }
        }
        
        function processPythonCode(codigo) {
            // Procesa el código Python simulando su ejecución real
            const lines = codigo.split('\n');
            let variables = {};
            let indentStack = [0]; // Para seguir el nivel de indentación
            let currentIndent = 0;
            let outputDelayMs = 200; // Retraso para simular procesamiento
            
            // Extraer y ejecutar las partes clave del código
            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                const trimmedLine = line.trimLeft();
                
                // Saltar líneas vacías o comentarios
                if (trimmedLine === '' || trimmedLine.startsWith('#')) continue;
                
                // Calcular indentación
                const indent = line.length - trimmedLine.length;
                
                // Gestión de bloques por indentación
                if (indent > currentIndent) {
                    indentStack.push(indent);
                } else if (indent < currentIndent) {
                    indentStack.pop();
                }
                currentIndent = indent;
                
                // Procesar línea según su contenido
                setTimeout(() => {
                    processLine(trimmedLine, variables, i+1);
                }, outputDelayMs * (i+1));
            }
        }
        
        function processLine(line, variables, lineNumber) {
            // Procesa una línea de código Python
            try {
                // Manejo de prints
                const printMatch = line.match(/print\s*\((.*)\)/);
                if (printMatch) {
                    let content = printMatch[1].trim();
                    
                    // Manejar strings literales
                    if ((content.startsWith('"') && content.endsWith('"')) || 
                        (content.startsWith("'") && content.endsWith("'"))) {
                        content = content.substring(1, content.length - 1);
                        mostrarEnTerminal(">>> " + content, 'string');
                        return;
                    }
                    
                    // Simular evaluación de expresiones
                    try {
                        // Reemplazar variables conocidas
                        for (const [name, value] of Object.entries(variables)) {
                            // Si el valor es una cadena, lo ponemos entre comillas para la evaluación
                            const replacementValue = typeof value === 'string' ? `"${value}"` : value;
                            content = content.replace(
                                new RegExp('\\b' + name + '\\b', 'g'), 
                                replacementValue
                            );
                        }
                        
                        // Evaluar expresiones aritméticas básicas y operaciones con cadenas
                        // Nota: Esto es simplificado y solo maneja casos básicos
                        let result;
                        if (content.includes('+') || content.includes('-') || 
                            content.includes('*') || content.includes('/')) {
                            // Sanitizamos para seguridad
                            content = content.replace(/[^0-9+\-*/." ]/g, '');
                            result = eval(content);
                            
                            // Mostrar con formato específico según tipo
                            if (typeof result === 'number') {
                                mostrarEnTerminal(">>> " + result, 'number');
                            } else {
                                mostrarEnTerminal(">>> " + result, 'string');
                            }
                        } else {
                            result = content;
                            mostrarEnTerminal(">>> " + result, 'string');
                        }
                    } catch (e) {
                        mostrarEnTerminal(">>> [Valor: " + content + "]", 'string');
                    }
                    return;
                }
                
                // Asignación de variables
                const assignmentMatch = line.match(/(\w+)\s*=\s*(.+)/);
                if (assignmentMatch) {
                    const varName = assignmentMatch[1];
                    let varValue = assignmentMatch[2].trim();
                    
                    // Evaluar el valor correcto de la variable
                    if ((varValue.startsWith('"') && varValue.endsWith('"')) || 
                        (varValue.startsWith("'") && varValue.endsWith("'"))) {
                        // Es una cadena
                        varValue = varValue.substring(1, varValue.length - 1);
                    } else if (!isNaN(varValue)) {
                        // Es un número
                        varValue = parseFloat(varValue);
                    } else if (varValue === 'True') {
                        varValue = true;
                    } else if (varValue === 'False') {
                        varValue = false;
                    }
                    
                    // Guardar variable
                    variables[varName] = varValue;
                    return;
                }
                
                // Detectar estructuras if-else
                if (line.startsWith('if ') || line.startsWith('elif ') || 
                    line.startsWith('else:') || line.startsWith('for ') || 
                    line.startsWith('while ') || line.startsWith('def ')) {
                    // Simular funcionamiento del bloque
                    mostrarEnTerminal(">>> [Procesando bloque: " + line + "]", 'processing');
                    return;
                }
                
            } catch (error) {
                mostrarEnTerminal(">>> [Error en línea " + lineNumber + ": " + error.message + "]", 'error');
            }
        }
        
        // Event listeners para los botones
        if (runCodeBtn) {
            runCodeBtn.addEventListener('click', ejecutarCodigo);
        }
        
        if (runCodeFromTerminalBtn) {
            runCodeFromTerminalBtn.addEventListener('click', ejecutarCodigo);
        }
        
        if (clearTerminalBtn) {
            clearTerminalBtn.addEventListener('click', function() {
                terminal.innerHTML = '<p class="output-prompt">Terminal limpia y lista para ejecutar código...</p>';
            });
        }
        
        // Funcionalidad para el corrector de IA
        const checkCodeWithAIBtn = document.getElementById('checkCodeWithAIBtn');
        const aiCorrectorOutput = document.getElementById('aiCorrectorOutput');
        
        if (checkCodeWithAIBtn) {
            checkCodeWithAIBtn.addEventListener('click', function() {
                const codigo = codeTextarea.value;
                if (!codigo.trim()) {
                    aiCorrectorOutput.innerHTML = '<p class="text-danger">Error: No hay código para verificar</p>';
                    return;
                }
                
                // Mostrar indicador de carga
                aiCorrectorOutput.innerHTML = '<p><i class="fas fa-spinner fa-spin"></i> Analizando tu código...</p>';
                
                // Simulación de análisis de código (en un entorno real, llamarías a OpenAI)
                setTimeout(() => {
                    checkCodeWithAI(codigo).then(feedback => {
                        aiCorrectorOutput.innerHTML = feedback;
                    });
                }, 1500);
            });
        }
        
        // Función para verificar código con IA
        async function checkCodeWithAI(codigo) {
            // Simulación de análisis con IA
            // En un entorno real, enviarías el código a tu API de OpenAI
            
            // Análisis básico (solo para demostración)
            let feedback = '<div class="mb-3">';
            
            // Verificar si hay funciones definidas
            if (codigo.includes('def ')) {
                feedback += '<p class="text-success"><i class="fas fa-check-circle me-1"></i> Definición de funciones correcta.</p>';
            } else if (codigo.toLowerCase().includes('función') || codigo.toLowerCase().includes('funcion')) {
                feedback += '<p class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i> Parece que intentas definir una función, pero no veo la palabra clave "def".</p>';
            }
            
            // Verificar indentación básica
            if (codigo.includes('    ')) {
                feedback += '<p class="text-success"><i class="fas fa-check-circle me-1"></i> La indentación parece correcta.</p>';
            } else if (codigo.includes('\t')) {
                feedback += '<p class="text-info"><i class="fas fa-info-circle me-1"></i> Estás utilizando tabulaciones para la indentación. Python funciona con ambas, pero es recomendable ser consistente.</p>';
            }
            
            // Verificar return en funciones
            if (codigo.includes('def ') && !codigo.includes('return')) {
                feedback += '<p class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i> Has definido una función pero no veo ninguna declaración "return". Asegúrate de que tu función devuelva algo si es necesario.</p>';
            }
            
            // Verificar bucles for
            if (codigo.includes('for ') && codigo.includes(' in ')) {
                feedback += '<p class="text-success"><i class="fas fa-check-circle me-1"></i> Utilizas correctamente la estructura de bucle "for".</p>';
            }
            
            // Un pequeño resumen
            feedback += '<hr><p class="fw-bold">Resumen:</p>';
            
            // Simplemente alternamos entre diferentes mensajes para la demostración
            const randomFeedback = Math.floor(Math.random() * 3);
            if (randomFeedback === 0) {
                feedback += '<p>Tu código parece prometedor, pero revisa los comentarios anteriores para mejorarlo antes de enviarlo.</p>';
            } else if (randomFeedback === 1) {
                feedback += '<p>El código está estructurado correctamente. Asegúrate de seguir las instrucciones del ejercicio.</p>';
            } else {
                feedback += '<p>Tu solución está en buen camino. Revisa si hay oportunidades para optimizar o mejorar la legibilidad.</p>';
            }
            
            feedback += '</div>';
            return feedback;
        }
    });
</script>
@endpush 