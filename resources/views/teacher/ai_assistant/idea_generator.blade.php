@extends('layouts.app')

@section('title', 'Generador de Ideas')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.ai_assistant.index') }}">Asistente de IA</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generador de Ideas</li>
                </ol>
            </nav>
            <h1 class="h2 mb-3">Generador de Ideas para Desafíos</h1>
            <p class="text-muted">Obtén ideas creativas y pedagógicamente sólidas para tus desafíos educativos.</p>
        </div>
    </div>

    <div class="row">
        <!-- Formulario de generación -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="idea-generator-form" method="POST" action="{{ route('teacher.ai_assistant.generate_idea') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Tema o Materia</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Ej: Programación, Matemáticas, Ciencias..." required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="objectives" class="form-label">Objetivos de Aprendizaje</label>
                            <textarea class="form-control" id="objectives" name="objectives" rows="3" placeholder="Describe los objetivos que quieres alcanzar (uno por línea)" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="age_level" class="form-label">Nivel de Edad</label>
                            <select class="form-select" id="age_level" name="age_level">
                                <option value="">Cualquier nivel</option>
                                <option value="primaria">Primaria (6-12 años)</option>
                                <option value="secundaria">Secundaria (12-16 años)</option>
                                <option value="bachillerato">Bachillerato (16-18 años)</option>
                                <option value="universidad">Universidad (18+ años)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="difficulty_level" class="form-label">Nivel de Dificultad</label>
                            <select class="form-select" id="difficulty_level" name="difficulty_level">
                                <option value="">Cualquier dificultad</option>
                                <option value="principiante">Principiante</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="challenge_type" class="form-label">Tipo de Desafío</label>
                            <select class="form-select" id="challenge_type" name="challenge_type" required>
                                <option value="" selected disabled>Selecciona un tipo</option>
                                <option value="python">Programación (Python)</option>
                                <option value="ai_prompt">Prompt Engineering</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="count" class="form-label">Número de ideas</label>
                            <select class="form-select" id="count" name="count">
                                <option value="1">1 idea</option>
                                <option value="2">2 ideas</option>
                                <option value="3" selected>3 ideas</option>
                                <option value="5">5 ideas</option>
                            </select>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" id="generate-btn" class="btn btn-primary">
                                <i class="fas fa-lightbulb me-2"></i> Generar Ideas
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
                        <h4 class="h5 mb-0">Ideas Generadas</h4>
                        <button id="copy-btn" class="btn btn-sm btn-outline-secondary" disabled>
                            <i class="fas fa-copy me-1"></i> Copiar Todas
                        </button>
                    </div>
                    
                    <div id="loading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Generando ideas creativas...</p>
                    </div>
                    
                    <div id="empty-state" class="text-center py-5">
                        <img src="{{ asset('images/idea-icon.svg') }}" alt="Generador de ideas" class="mb-4" style="max-width: 120px; opacity: 0.7;">
                        <h5>Todavía no has generado ninguna idea</h5>
                        <p class="text-muted">Completa el formulario y haz clic en "Generar Ideas" para ver los resultados aquí.</p>
                    </div>
                    
                    <div id="idea-results" class="d-none">
                        <div class="accordion" id="ideasAccordion">
                            <!-- Ideas generadas se mostrarán aquí -->
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
                    <h4 class="h5 mb-3">Consejos para Mejores Resultados</h4>
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="display-6 text-primary"><i class="fas fa-list"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Sé específico con los objetivos</h5>
                                    <p class="small text-muted">Define claramente qué habilidades o conocimientos quieres que desarrollen tus estudiantes.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="display-6 text-primary"><i class="fas fa-graduation-cap"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Considera el nivel de edad</h5>
                                    <p class="small text-muted">Especificar el rango de edad ayuda a obtener ideas más adecuadas para tu audiencia.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="display-6 text-primary"><i class="fas fa-edit"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="h6">Personaliza las ideas</h5>
                                    <p class="small text-muted">Usa las ideas generadas como punto de partida y adáptalas a tus necesidades específicas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template para ideas generadas -->
<template id="idea-template">
    <div class="accordion-item border-0 mb-3">
        <h2 class="accordion-header" id="heading-{id}">
            <button class="accordion-button collapsed shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{id}" aria-expanded="false" aria-controls="collapse-{id}">
                <span class="idea-title">Idea {num}: {title}</span>
            </button>
        </h2>
        <div id="collapse-{id}" class="accordion-collapse collapse" aria-labelledby="heading-{id}" data-bs-parent="#ideasAccordion">
            <div class="accordion-body bg-light rounded-bottom p-4">
                <div class="idea-content"></div>
                
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-sm btn-outline-primary copy-idea-btn me-2">
                        <i class="fas fa-copy me-1"></i> Copiar
                    </button>
                    <button class="btn btn-sm btn-outline-success use-idea-btn">
                        <i class="fas fa-check me-1"></i> Usar Esta Idea
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('idea-generator-form');
        const generateBtn = document.getElementById('generate-btn');
        const copyBtn = document.getElementById('copy-btn');
        const loadingElement = document.getElementById('loading');
        const ideaResults = document.getElementById('idea-results');
        const emptyState = document.getElementById('empty-state');
        const ideasAccordion = document.getElementById('ideasAccordion');
        const ideaTemplate = document.getElementById('idea-template');
        
        // Array para almacenar las ideas generadas
        let generatedIdeas = [];
        
        // Manejar el envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar cargando
            emptyState.classList.add('d-none');
            ideaResults.classList.add('d-none');
            loadingElement.classList.remove('d-none');
            
            // Deshabilitar botón
            generateBtn.disabled = true;
            copyBtn.disabled = true;
            
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
                generateBtn.disabled = false;
                
                if (data.success && data.ideas) {
                    // Guardar ideas
                    generatedIdeas = data.ideas;
                    
                    // Limpiar resultados anteriores
                    ideasAccordion.innerHTML = '';
                    
                    // Mostrar ideas
                    displayIdeas(data.ideas);
                    
                    // Mostrar sección de resultados
                    ideaResults.classList.remove('d-none');
                    copyBtn.disabled = false;
                } else {
                    // Mostrar error
                    alert('Error: ' + (data.error || 'No se pudieron generar ideas.'));
                    emptyState.classList.remove('d-none');
                }
            })
            .catch(error => {
                // Ocultar cargando
                loadingElement.classList.add('d-none');
                
                // Habilitar botón
                generateBtn.disabled = false;
                
                // Mostrar error
                console.error('Error:', error);
                alert('Hubo un error al procesar la solicitud.');
                emptyState.classList.remove('d-none');
            });
        });
        
        // Función para mostrar las ideas
        function displayIdeas(ideas) {
            ideas.forEach((idea, index) => {
                // Clonar la plantilla
                const template = ideaTemplate.content.cloneNode(true);
                const id = 'idea-' + (index + 1);
                
                // Reemplazar placeholders
                template.querySelector('.accordion-item').id = id;
                template.querySelector('.accordion-header').id = 'heading-' + id;
                template.querySelector('.accordion-button').setAttribute('data-bs-target', '#collapse-' + id);
                template.querySelector('.accordion-button').setAttribute('aria-controls', 'collapse-' + id);
                template.querySelector('.accordion-collapse').id = 'collapse-' + id;
                template.querySelector('.accordion-collapse').setAttribute('aria-labelledby', 'heading-' + id);
                
                // Establecer título
                const titleElement = template.querySelector('.idea-title');
                titleElement.textContent = titleElement.textContent
                    .replace('{num}', index + 1)
                    .replace('{title}', idea.title || 'Sin título');
                
                // Establecer contenido
                const contentElement = template.querySelector('.idea-content');
                
                // Preparar el contenido HTML
                let contentHtml = '';
                
                if (idea.description) {
                    contentHtml += '<div class="mb-3"><strong>Descripción:</strong><p>' + idea.description + '</p></div>';
                }
                
                if (idea.objectives) {
                    contentHtml += '<div class="mb-3"><strong>Objetivos de Aprendizaje:</strong><ul>';
                    
                    // Si objectives es un array
                    if (Array.isArray(idea.objectives)) {
                        idea.objectives.forEach(obj => {
                            contentHtml += '<li>' + obj + '</li>';
                        });
                    } else {
                        // Si es un string, dividir por líneas
                        const objectives = idea.objectives.split('\n');
                        objectives.forEach(obj => {
                            if (obj.trim()) {
                                contentHtml += '<li>' + obj.trim() + '</li>';
                            }
                        });
                    }
                    
                    contentHtml += '</ul></div>';
                }
                
                if (idea.instructions) {
                    contentHtml += '<div class="mb-3"><strong>Instrucciones Sugeridas:</strong><p>' + idea.instructions + '</p></div>';
                }
                
                if (idea.materials) {
                    contentHtml += '<div class="mb-3"><strong>Materiales Necesarios:</strong><ul>';
                    
                    // Si materials es un array
                    if (Array.isArray(idea.materials)) {
                        idea.materials.forEach(mat => {
                            contentHtml += '<li>' + mat + '</li>';
                        });
                    } else {
                        // Si es un string, dividir por líneas
                        const materials = idea.materials.split('\n');
                        materials.forEach(mat => {
                            if (mat.trim()) {
                                contentHtml += '<li>' + mat.trim() + '</li>';
                            }
                        });
                    }
                    
                    contentHtml += '</ul></div>';
                }
                
                if (idea.evaluation_criteria) {
                    contentHtml += '<div class="mb-3"><strong>Criterios de Evaluación Sugeridos:</strong><ul>';
                    
                    // Si evaluation_criteria es un array
                    if (Array.isArray(idea.evaluation_criteria)) {
                        idea.evaluation_criteria.forEach(crit => {
                            contentHtml += '<li>' + crit + '</li>';
                        });
                    } else {
                        // Si es un string, dividir por líneas
                        const criteria = idea.evaluation_criteria.split('\n');
                        criteria.forEach(crit => {
                            if (crit.trim()) {
                                contentHtml += '<li>' + crit.trim() + '</li>';
                            }
                        });
                    }
                    
                    contentHtml += '</ul></div>';
                }
                
                if (idea.tips) {
                    contentHtml += '<div class="mb-3"><strong>Consejos Adicionales:</strong><p>' + idea.tips + '</p></div>';
                }
                
                // Si contentHtml está vacío, mostrar un mensaje
                if (!contentHtml) {
                    contentHtml = '<p>No hay detalles disponibles para esta idea.</p>';
                }
                
                contentElement.innerHTML = contentHtml;
                
                // Agregar al accordion
                ideasAccordion.appendChild(template);
                
                // Inicializar botones de cada idea
                const ideaElement = ideasAccordion.lastElementChild;
                
                // Botón de copiar
                const copyIdeaBtn = ideaElement.querySelector('.copy-idea-btn');
                copyIdeaBtn.addEventListener('click', function() {
                    const content = getIdeaText(idea);
                    copyToClipboard(content, copyIdeaBtn);
                });
                
                // Botón de usar idea
                const useIdeaBtn = ideaElement.querySelector('.use-idea-btn');
                useIdeaBtn.addEventListener('click', function() {
                    selectIdea(this, index);
                });
            });
        }
        
        // Obtener texto de una idea para copiar
        function getIdeaText(idea) {
            let text = '';
            
            text += 'IDEA: ' + (idea.title || 'Sin título') + '\n\n';
            
            if (idea.description) {
                text += 'DESCRIPCIÓN:\n' + idea.description + '\n\n';
            }
            
            if (idea.objectives) {
                text += 'OBJETIVOS DE APRENDIZAJE:\n';
                
                if (Array.isArray(idea.objectives)) {
                    idea.objectives.forEach(obj => {
                        text += '- ' + obj + '\n';
                    });
                } else {
                    const objectives = idea.objectives.split('\n');
                    objectives.forEach(obj => {
                        if (obj.trim()) {
                            text += '- ' + obj.trim() + '\n';
                        }
                    });
                }
                
                text += '\n';
            }
            
            if (idea.instructions) {
                text += 'INSTRUCCIONES SUGERIDAS:\n' + idea.instructions + '\n\n';
            }
            
            if (idea.materials) {
                text += 'MATERIALES NECESARIOS:\n';
                
                if (Array.isArray(idea.materials)) {
                    idea.materials.forEach(mat => {
                        text += '- ' + mat + '\n';
                    });
                } else {
                    const materials = idea.materials.split('\n');
                    materials.forEach(mat => {
                        if (mat.trim()) {
                            text += '- ' + mat.trim() + '\n';
                        }
                    });
                }
                
                text += '\n';
            }
            
            if (idea.evaluation_criteria) {
                text += 'CRITERIOS DE EVALUACIÓN SUGERIDOS:\n';
                
                if (Array.isArray(idea.evaluation_criteria)) {
                    idea.evaluation_criteria.forEach(crit => {
                        text += '- ' + crit + '\n';
                    });
                } else {
                    const criteria = idea.evaluation_criteria.split('\n');
                    criteria.forEach(crit => {
                        if (crit.trim()) {
                            text += '- ' + crit.trim() + '\n';
                        }
                    });
                }
                
                text += '\n';
            }
            
            if (idea.tips) {
                text += 'CONSEJOS ADICIONALES:\n' + idea.tips + '\n';
            }
            
            return text;
        }
        
        // Función para copiar texto al portapapeles
        function copyToClipboard(text, button) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    // Guardar texto original
                    const originalText = button.innerHTML;
                    
                    // Cambiar texto del botón temporalmente
                    button.innerHTML = '<i class="fas fa-check me-1"></i> Copiado';
                    
                    // Restaurar después de 2 segundos
                    setTimeout(() => {
                        button.innerHTML = originalText;
                    }, 2000);
                })
                .catch(err => {
                    console.error('Error al copiar: ', err);
                    alert('No se pudo copiar el texto. Inténtalo de nuevo.');
                });
        }
        
        // Botón para copiar todas las ideas
        copyBtn.addEventListener('click', function() {
            if (generatedIdeas.length === 0) return;
            
            let allIdeasText = '';
            
            // Concatenar todas las ideas
            generatedIdeas.forEach((idea, index) => {
                allIdeasText += '==================\n';
                allIdeasText += 'IDEA ' + (index + 1) + '\n';
                allIdeasText += '==================\n\n';
                allIdeasText += getIdeaText(idea);
                allIdeasText += '\n\n';
            });
            
            // Copiar al portapapeles
            copyToClipboard(allIdeasText, copyBtn);
        });
        
        // Seleccionar una idea y enviar al formulario principal
        function selectIdea(button, ideaIndex) {
            console.log('Seleccionando idea con índice:', ideaIndex);
            
            // Resaltar el botón seleccionado
            document.querySelectorAll('.use-idea-btn').forEach(btn => {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-success');
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Usar Esta Idea';
            });
            
            button.classList.remove('btn-outline-success');
            button.classList.add('btn-success');
            button.innerHTML = '<i class="fas fa-check-circle me-2"></i>Idea Seleccionada';
            
            // Obtener la idea directamente del array de ideas generadas si está disponible
            if (generatedIdeas && generatedIdeas[ideaIndex]) {
                const idea = generatedIdeas[ideaIndex];
                console.log('Idea encontrada en el array:', idea);
                
                // Crear un objeto simplificado para enviarlo a la ventana principal
                const ideaData = {
                    title: idea.title || '',
                    description: idea.description || '',
                    instructions: idea.instructions || '',
                    hints: idea.tips || '', // Nota: tips se mapea a hints
                    objectives: Array.isArray(idea.objectives) ? idea.objectives.join('\n') : idea.objectives || '',
                    materials: Array.isArray(idea.materials) ? idea.materials.join('\n') : idea.materials || '',
                    starter_code: idea.starter_code || '',
                    solution_code: idea.solution_code || '',
                    example_prompt: idea.example_prompt || '',
                    difficulty: idea.difficulty || 'intermedio',
                    create_directly: true // Bandera para indicar creación directa
                };
                
                console.log('Datos preparados para enviar:', ideaData);
                
                // Guardar en almacenamiento local para que pueda ser recuperado por la ventana principal
                localStorage.setItem('selectedIdeaData', JSON.stringify(ideaData));
                localStorage.setItem('selectedIdea', JSON.stringify(ideaData));
                
                // Método alternativo: crear un formulario y enviarlo directamente
                alert('Creando ejercicio directamente desde la idea seleccionada...');
                const submitForm = document.createElement('form');
                submitForm.method = 'POST';
                submitForm.action = '/teaching-challenges/{{ request()->query('challenge_id', '0') }}/exercises';
                submitForm.target = '_parent'; // Enviar al padre
                submitForm.style.display = 'none';
                
                // CSRF Token - solicitar al padre
                window.parent.postMessage({
                    type: 'request-csrf-token'
                }, '*');
                
                // Escuchar el token
                window.addEventListener('message', function(event) {
                    if (event.data && event.data.type === 'csrf-token-response') {
                        const csrfToken = event.data.token;
                        
                        // Crear el campo de token CSRF
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrfToken;
                        submitForm.appendChild(tokenInput);
                        
                        // Agregar todos los campos de la idea
                        for (const key in ideaData) {
                            if (key !== 'create_directly') { // Excluir la bandera
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = key;
                                input.value = ideaData[key] || '';
                                submitForm.appendChild(input);
                            }
                        }
                        
                        // Agregar al documento y enviar
                        document.body.appendChild(submitForm);
                        submitForm.submit();
                    }
                }, { once: true }); // Escuchar solo una vez
                
                // Mostrar mensaje de confirmación
                alert('¡Idea seleccionada! Preparando formulario para crear ejercicio...');
            } else {
                console.error('No se pudo encontrar la idea en el array generatedIdeas');
                alert('Error al seleccionar la idea. Por favor, intenta generar ideas nuevamente.');
            }
        }
        
        // Observar cambios en el DOM para agregar botones a nuevas ideas generadas
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    // Verificar si los nodos agregados contienen ideas
                    if (document.querySelector('.accordion-item')) {
                        addUseIdeaButtons();
                    }
                }
            });
        });
        
        // Iniciar observación del contenedor de resultados
        const resultsContainer = document.getElementById('results-container');
        if (resultsContainer) {
            observer.observe(resultsContainer, { childList: true, subtree: true });
        }
        
        // Agregar botones a las ideas existentes
        addUseIdeaButtons();
    });
</script>
@endsection 