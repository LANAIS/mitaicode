@extends('layouts.app')

@section('title', 'Verificador de Calidad')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.ai_assistant.index') }}">Asistente de IA</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Verificador de Calidad</li>
                </ol>
            </nav>
            <h1 class="h2 mb-3">Verificador de Calidad</h1>
            <p class="text-muted">Evalúa la calidad y adecuación de tus ejercicios antes de publicarlos a los estudiantes.</p>
        </div>
    </div>

    <div class="row">
        <!-- Formulario de verificación -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="quality-checker-form" method="POST" action="{{ route('teacher.ai_assistant.check_quality') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Título del Ejercicio</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Ej: Creación de un algoritmo de ordenamiento" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción Breve</label>
                            <textarea class="form-control" id="description" name="description" rows="2" placeholder="Breve descripción del ejercicio" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instrucciones</label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="6" placeholder="Instrucciones detalladas para el estudiante" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="challenge_type" class="form-label">Tipo de Desafío</label>
                            <select class="form-select" id="challenge_type" name="challenge_type" required>
                                <option value="" selected disabled>Selecciona un tipo</option>
                                <option value="python">Programación (Python)</option>
                                <option value="ai_prompt">Prompt Engineering</option>
                            </select>
                        </div>
                        
                        <div class="mb-3 d-none" id="prompt-section">
                            <label for="example_prompt" class="form-label">Ejemplo de Prompt</label>
                            <textarea class="form-control" id="example_prompt" name="example_prompt" rows="3" placeholder="Ejemplo de prompt si es un ejercicio de Prompt Engineering"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="difficulty_level" class="form-label">Nivel de Dificultad</label>
                            <select class="form-select" id="difficulty_level" name="difficulty_level" required>
                                <option value="" selected disabled>Selecciona un nivel</option>
                                <option value="principiante">Principiante</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                            </select>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" id="check-btn" class="btn btn-warning">
                                <i class="fas fa-check-double me-2"></i> Verificar Calidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Área de resultados -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="h5 mb-0">Resultado del Análisis</h4>
                        <button id="copy-btn" class="btn btn-sm btn-outline-secondary" disabled>
                            <i class="fas fa-copy me-1"></i> Copiar Feedback
                        </button>
                    </div>
                    
                    <div id="loading" class="text-center py-5 d-none">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Analizando la calidad del ejercicio...</p>
                    </div>
                    
                    <div id="empty-state" class="text-center py-5">
                        <img src="{{ asset('images/quality-icon.svg') }}" alt="Verificador de calidad" class="mb-4" style="max-width: 120px; opacity: 0.7;">
                        <h5>Todavía no has verificado ningún ejercicio</h5>
                        <p class="text-muted">Completa el formulario y haz clic en "Verificar Calidad" para recibir feedback.</p>
                    </div>
                    
                    <div id="results-container" class="d-none">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-3">
                                <h5 class="h6">Puntuación de Calidad</h5>
                                <span id="quality-score" class="badge bg-success fs-6">85/100</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div id="quality-bar" class="progress-bar bg-success" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="h6">Nivel de Dificultad</h5>
                                <span id="difficulty-badge" class="badge bg-info">Adecuado</span>
                            </div>
                            <p id="difficulty-message" class="text-muted small">El nivel de dificultad seleccionado es adecuado para el contenido del ejercicio.</p>
                        </div>
                        
                        <div class="mb-3">
                            <h5 class="h6 mb-3">Fortalezas</h5>
                            <ul id="strengths-list" class="list-group list-group-flush">
                                <li class="list-group-item px-0 py-2 border-0"><i class="fas fa-check-circle text-success me-2"></i> Las instrucciones son claras y concisas.</li>
                                <li class="list-group-item px-0 py-2 border-0"><i class="fas fa-check-circle text-success me-2"></i> El ejercicio tiene un objetivo pedagógico bien definido.</li>
                                <li class="list-group-item px-0 py-2 border-0"><i class="fas fa-check-circle text-success me-2"></i> La estructura es adecuada para el nivel seleccionado.</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <h5 class="h6 mb-3">Áreas de Mejora</h5>
                            <ul id="areas-list" class="list-group list-group-flush">
                                <li class="list-group-item px-0 py-2 border-0"><i class="fas fa-exclamation-circle text-warning me-2"></i> Considera añadir ejemplos concretos para facilitar la comprensión.</li>
                                <li class="list-group-item px-0 py-2 border-0"><i class="fas fa-exclamation-circle text-warning me-2"></i> Podrías incluir pistas para estudiantes con dificultades.</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <h5 class="h6 mb-3">Recomendaciones</h5>
                            <ul id="recommendations-list" class="list-group list-group-flush">
                                <li class="list-group-item px-0 py-2 border-0"><i class="fas fa-lightbulb text-primary me-2"></i> Añade criterios de evaluación específicos para que los estudiantes sepan qué se espera de ellos.</li>
                                <li class="list-group-item px-0 py-2 border-0"><i class="fas fa-lightbulb text-primary me-2"></i> Incluye recursos adicionales para estudiantes avanzados.</li>
                                <li class="list-group-item px-0 py-2 border-0"><i class="fas fa-lightbulb text-primary me-2"></i> Considera dividir la tarea en pasos más pequeños para estudiantes principiantes.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sección de consejos -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="h5 mb-3">Consejos para Mejorar tus Ejercicios</h4>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="display-6 text-primary"><i class="fas fa-bullseye"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Define objetivos claros</h5>
                                    <p class="small text-muted">Cada ejercicio debe tener objetivos de aprendizaje específicos y medibles. Asegúrate de que los estudiantes entiendan qué habilidades desarrollarán.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="display-6 text-primary"><i class="fas fa-users"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Personaliza para diferentes niveles</h5>
                                    <p class="small text-muted">Incluye variantes o extensiones para estudiantes avanzados y scaffolding (andamiaje) para quienes necesitan más apoyo.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="display-6 text-primary"><i class="fas fa-tasks"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Estructura por pasos</h5>
                                    <p class="small text-muted">Divide ejercicios complejos en pasos más pequeños y manejables para facilitar la comprensión y evitar la sobrecarga cognitiva.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="display-6 text-primary"><i class="fas fa-chart-line"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Establece criterios de evaluación</h5>
                                    <p class="small text-muted">Define claramente cómo se evaluará el ejercicio para que los estudiantes sepan exactamente qué se espera de ellos.</p>
                                </div>
                            </div>
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
        const form = document.getElementById('quality-checker-form');
        const checkBtn = document.getElementById('check-btn');
        const copyBtn = document.getElementById('copy-btn');
        const loadingElement = document.getElementById('loading');
        const resultsContainer = document.getElementById('results-container');
        const emptyState = document.getElementById('empty-state');
        const challengeType = document.getElementById('challenge_type');
        const promptSection = document.getElementById('prompt-section');
        
        // Mostrar/ocultar sección de prompt según el tipo de desafío
        challengeType.addEventListener('change', function() {
            if (this.value === 'ai_prompt') {
                promptSection.classList.remove('d-none');
                document.getElementById('example_prompt').setAttribute('required', '');
            } else {
                promptSection.classList.add('d-none');
                document.getElementById('example_prompt').removeAttribute('required');
            }
        });
        
        // Manejar el envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar cargando
            emptyState.classList.add('d-none');
            resultsContainer.classList.add('d-none');
            loadingElement.classList.remove('d-none');
            
            // Deshabilitar botón
            checkBtn.disabled = true;
            
            // Obtener datos del formulario
            const formData = new FormData(form);
            
            // Hacer solicitud AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ocultar cargando
                loadingElement.classList.add('d-none');
                
                // Habilitar botón
                checkBtn.disabled = false;
                
                if (data.success) {
                    // Mostrar resultados
                    updateResults(data.feedback);
                    resultsContainer.classList.remove('d-none');
                    copyBtn.disabled = false;
                } else {
                    // Mostrar error
                    alert('Error: ' + (data.error || 'No se pudo completar el análisis.'));
                    emptyState.classList.remove('d-none');
                }
            })
            .catch(error => {
                // Ocultar cargando
                loadingElement.classList.add('d-none');
                
                // Habilitar botón
                checkBtn.disabled = false;
                
                // Mostrar error
                console.error('Error:', error);
                alert('Hubo un error al procesar la solicitud.');
                emptyState.classList.remove('d-none');
            });
        });
        
        // Función para actualizar resultados
        function updateResults(feedback) {
            // Actualizar puntuación
            const qualityScore = document.getElementById('quality-score');
            const qualityBar = document.getElementById('quality-bar');
            qualityScore.textContent = feedback.quality_score + '/100';
            qualityBar.style.width = feedback.quality_score + '%';
            
            // Determinar clase de color según la puntuación
            let scoreClass = 'bg-success';
            if (feedback.quality_score < 60) {
                scoreClass = 'bg-danger';
            } else if (feedback.quality_score < 80) {
                scoreClass = 'bg-warning';
            }
            
            qualityScore.className = 'badge fs-6 ' + scoreClass;
            qualityBar.className = 'progress-bar ' + scoreClass;
            
            // Actualizar dificultad
            const difficultyBadge = document.getElementById('difficulty-badge');
            const difficultyMessage = document.getElementById('difficulty-message');
            
            if (feedback.appropriate_difficulty) {
                difficultyBadge.textContent = 'Adecuado';
                difficultyBadge.className = 'badge bg-info';
                difficultyMessage.textContent = 'El nivel de dificultad seleccionado es adecuado para el contenido del ejercicio.';
            } else {
                difficultyBadge.textContent = 'Revisar';
                difficultyBadge.className = 'badge bg-warning';
                difficultyMessage.textContent = 'El nivel de dificultad seleccionado podría no ser adecuado. Revisa las recomendaciones.';
            }
            
            // Actualizar listas
            updateList('strengths-list', feedback.feedback.strengths, 'check-circle', 'text-success');
            updateList('areas-list', feedback.feedback.areas_to_improve, 'exclamation-circle', 'text-warning');
            updateList('recommendations-list', feedback.feedback.recommendations, 'lightbulb', 'text-primary');
        }
        
        // Actualizar una lista de feedback
        function updateList(elementId, items, icon, iconClass) {
            const list = document.getElementById(elementId);
            list.innerHTML = '';
            
            items.forEach(item => {
                const li = document.createElement('li');
                li.className = 'list-group-item px-0 py-2 border-0';
                li.innerHTML = `<i class="fas fa-${icon} ${iconClass} me-2"></i> ${item}`;
                list.appendChild(li);
            });
        }
        
        // Funcionalidad del botón copiar
        copyBtn.addEventListener('click', function() {
            // Preparar el texto a copiar
            let textToCopy = '';
            
            // Título
            textToCopy += 'ANÁLISIS DE CALIDAD DEL EJERCICIO\n\n';
            
            // Puntuación
            textToCopy += 'Puntuación: ' + document.getElementById('quality-score').textContent + '\n';
            textToCopy += 'Dificultad: ' + document.getElementById('difficulty-badge').textContent + '\n';
            textToCopy += document.getElementById('difficulty-message').textContent + '\n\n';
            
            // Fortalezas
            textToCopy += 'FORTALEZAS:\n';
            document.querySelectorAll('#strengths-list li').forEach(li => {
                textToCopy += '✓ ' + li.textContent.trim().replace(/\n/g, '') + '\n';
            });
            textToCopy += '\n';
            
            // Áreas de mejora
            textToCopy += 'ÁREAS DE MEJORA:\n';
            document.querySelectorAll('#areas-list li').forEach(li => {
                textToCopy += '! ' + li.textContent.trim().replace(/\n/g, '') + '\n';
            });
            textToCopy += '\n';
            
            // Recomendaciones
            textToCopy += 'RECOMENDACIONES:\n';
            document.querySelectorAll('#recommendations-list li').forEach(li => {
                textToCopy += '» ' + li.textContent.trim().replace(/\n/g, '') + '\n';
            });
            
            // Copiar al portapapeles
            navigator.clipboard.writeText(textToCopy)
                .then(() => {
                    // Feedback visual para la copia
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check me-1"></i> Copiado';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                })
                .catch(err => {
                    console.error('Error al copiar: ', err);
                    alert('No se pudo copiar el texto. Inténtalo de nuevo.');
                });
        });
    });
</script>
@endsection 