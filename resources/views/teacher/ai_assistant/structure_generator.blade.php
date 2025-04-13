@extends('layouts.app')

@section('title', 'Generador de Estructura de Desafíos')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.ai_assistant.index') }}">Asistente de IA</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generador de Estructura</li>
                </ol>
            </nav>
            <h1 class="h2 mb-3">Generador de Estructura de Desafíos</h1>
            <p class="text-muted">Crea una estructura organizada para tus desafíos educativos con todas las secciones necesarias.</p>
        </div>
    </div>

    <div class="row">
        <!-- Formulario de generación -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="structure-generator-form" method="POST" action="{{ route('teacher.ai_assistant.generate_structure') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="challenge_title" class="form-label">Título del desafío</label>
                            <input type="text" class="form-control" id="challenge_title" name="challenge_title" placeholder="Ej: Creación de un videojuego educativo con Scratch" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="main_topic" class="form-label">Tema principal</label>
                            <input type="text" class="form-control" id="main_topic" name="main_topic" placeholder="Ej: Programación, Pensamiento Computacional, etc." required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="educational_level" class="form-label">Nivel educativo</label>
                            <select class="form-select" id="educational_level" name="educational_level" required>
                                <option value="" selected disabled>Selecciona un nivel educativo</option>
                                <option value="primaria">Primaria</option>
                                <option value="secundaria">Secundaria</option>
                                <option value="bachillerato">Bachillerato</option>
                                <option value="universidad">Universidad</option>
                                <option value="formacion_profesional">Formación Profesional</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="challenge_type" class="form-label">Tipo de desafío</label>
                            <select class="form-select" id="challenge_type" name="challenge_type" required>
                                <option value="" selected disabled>Selecciona un tipo de desafío</option>
                                <option value="proyecto">Proyecto completo</option>
                                <option value="actividad">Actividad individual</option>
                                <option value="problema">Resolución de problema</option>
                                <option value="investigacion">Investigación</option>
                                <option value="hackathon">Mini-hackathon</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="learning_objectives" class="form-label">Objetivos de aprendizaje</label>
                            <textarea class="form-control" id="learning_objectives" name="learning_objectives" rows="3" placeholder="Describe los principales objetivos de aprendizaje (uno por línea)" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="time_allocation" class="form-label">Duración aproximada</label>
                            <select class="form-select" id="time_allocation" name="time_allocation" required>
                                <option value="" selected disabled>Selecciona la duración</option>
                                <option value="una_sesion">Una sesión (1-2 horas)</option>
                                <option value="varias_sesiones">Varias sesiones (2-5 horas)</option>
                                <option value="semana">Una semana (5-10 horas)</option>
                                <option value="quincena">Dos semanas o más</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="required_resources" class="form-label">Recursos necesarios</label>
                            <input type="text" class="form-control" id="required_resources" name="required_resources" placeholder="Ej: Ordenadores, software específico, materiales, etc.">
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="include_evaluation" name="include_evaluation" value="1" checked>
                            <label class="form-check-label" for="include_evaluation">Incluir criterios de evaluación</label>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="include_differentiation" name="include_differentiation" value="1" checked>
                            <label class="form-check-label" for="include_differentiation">Incluir estrategias de diferenciación</label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="additional_notes" class="form-label">Notas adicionales (opcional)</label>
                            <textarea class="form-control" id="additional_notes" name="additional_notes" rows="2" placeholder="Cualquier consideración específica que quieras incluir"></textarea>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" id="generate-btn" class="btn btn-primary">
                                <i class="fas fa-file-alt me-2"></i> Generar Estructura
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
                        <h4 class="h5 mb-0">Estructura generada</h4>
                        <div class="btn-group">
                            <button id="copy-btn" class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="fas fa-copy me-1"></i> Copiar
                            </button>
                            <button id="download-btn" class="btn btn-sm btn-outline-primary" disabled>
                                <i class="fas fa-download me-1"></i> Descargar
                            </button>
                        </div>
                    </div>
                    
                    <div id="loading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Generando estructura del desafío...</p>
                    </div>
                    
                    <div id="empty-state" class="text-center py-5">
                        <img src="{{ asset('images/structure-icon.svg') }}" alt="Generador de estructura" class="mb-4" style="max-width: 120px; opacity: 0.7;">
                        <h5>Aún no has generado ninguna estructura</h5>
                        <p class="text-muted">Completa el formulario y haz clic en "Generar Estructura" para ver el resultado aquí.</p>
                    </div>
                    
                    <div id="result-container" class="d-none">
                        <div class="nav nav-tabs mb-3" id="structure-tabs" role="tablist">
                            <button class="nav-link active" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview" type="button" role="tab" aria-controls="preview" aria-selected="true">Vista Previa</button>
                            <button class="nav-link" id="markdown-tab" data-bs-toggle="tab" data-bs-target="#markdown" type="button" role="tab" aria-controls="markdown" aria-selected="false">Markdown</button>
                        </div>
                        
                        <div class="tab-content" id="structure-content">
                            <div class="tab-pane fade show active" id="preview" role="tabpanel" aria-labelledby="preview-tab">
                                <div id="structure-preview" class="mb-4 p-2" style="max-height: 500px; overflow-y: auto;"></div>
                            </div>
                            <div class="tab-pane fade" id="markdown" role="tabpanel" aria-labelledby="markdown-tab">
                                <div class="bg-light p-3 rounded">
                                    <pre id="structure-markdown" class="mb-0" style="max-height: 500px; overflow-y: auto; white-space: pre-wrap;"></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button id="regenerate-btn" class="btn btn-outline-primary">
                                <i class="fas fa-redo me-2"></i> Regenerar
                            </button>
                            <button id="save-template-btn" class="btn btn-outline-success">
                                <i class="fas fa-save me-2"></i> Guardar como plantilla
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sección de plantillas guardadas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="h5 mb-3">Plantillas guardadas</h4>
                    <div id="templates-container" class="row row-cols-1 row-cols-md-3 g-4">
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Desafío de programación básica</h5>
                                    <p class="card-text text-muted small">Primaria | Proyecto | Una sesión</p>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <button class="btn btn-sm btn-outline-primary">Usar esta plantilla</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Hackathon de soluciones tecnológicas</h5>
                                    <p class="card-text text-muted small">Bachillerato | Hackathon | Dos semanas</p>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <button class="btn btn-sm btn-outline-primary">Usar esta plantilla</button>
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
        const form = document.getElementById('structure-generator-form');
        const generateBtn = document.getElementById('generate-btn');
        const regenerateBtn = document.getElementById('regenerate-btn');
        const copyBtn = document.getElementById('copy-btn');
        const downloadBtn = document.getElementById('download-btn');
        const saveTemplateBtn = document.getElementById('save-template-btn');
        const loadingElement = document.getElementById('loading');
        const resultContainer = document.getElementById('result-container');
        const emptyState = document.getElementById('empty-state');
        const structurePreview = document.getElementById('structure-preview');
        const structureMarkdown = document.getElementById('structure-markdown');
        
        // Guardar estructura generada
        let generatedStructure = null;
        
        // Manejar el envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar cargando
            emptyState.classList.add('d-none');
            resultContainer.classList.add('d-none');
            loadingElement.classList.remove('d-none');
            
            // Deshabilitar botones
            copyBtn.disabled = true;
            downloadBtn.disabled = true;
            
            // Obtener datos del formulario
            const formData = new FormData(form);
            
            // Simulamos una solicitud AJAX (reemplazar con la solicitud real)
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
                
                // Guardar los datos
                generatedStructure = data;
                
                // Mostrar el resultado
                if (data.html && data.markdown) {
                    structurePreview.innerHTML = data.html;
                    structureMarkdown.textContent = data.markdown;
                    
                    // Mostrar contenedor de resultados
                    resultContainer.classList.remove('d-none');
                    
                    // Habilitar botones
                    copyBtn.disabled = false;
                    downloadBtn.disabled = false;
                } else {
                    alert('No se pudo generar la estructura. Por favor, intenta de nuevo.');
                    emptyState.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingElement.classList.add('d-none');
                alert('Hubo un error al generar la estructura. Por favor, intenta de nuevo.');
                emptyState.classList.remove('d-none');
            });
        });
        
        // Funcionalidad del botón regenerar
        regenerateBtn.addEventListener('click', function() {
            form.dispatchEvent(new Event('submit'));
        });
        
        // Funcionalidad del botón copiar
        copyBtn.addEventListener('click', function() {
            const currentTab = document.querySelector('#structure-tabs .nav-link.active').getAttribute('id');
            let contentToCopy = '';
            
            if (currentTab === 'preview-tab') {
                contentToCopy = structurePreview.innerText;
            } else {
                contentToCopy = structureMarkdown.textContent;
            }
            
            navigator.clipboard.writeText(contentToCopy).then(() => {
                // Feedback visual para la copia
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check me-1"></i> Copiado';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
        });
        
        // Funcionalidad del botón descargar
        downloadBtn.addEventListener('click', function() {
            if (!generatedStructure) return;
            
            const currentTab = document.querySelector('#structure-tabs .nav-link.active').getAttribute('id');
            let content = '';
            let filename = '';
            let type = '';
            
            if (currentTab === 'preview-tab') {
                content = `<!DOCTYPE html><html><head><title>${generatedStructure.title || 'Estructura de Desafío'}</title></head><body>${structurePreview.innerHTML}</body></html>`;
                filename = 'estructura_desafio.html';
                type = 'text/html';
            } else {
                content = structureMarkdown.textContent;
                filename = 'estructura_desafio.md';
                type = 'text/markdown';
            }
            
            const blob = new Blob([content], { type });
            const url = URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            setTimeout(() => {
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 0);
        });
        
        // Funcionalidad del botón guardar como plantilla (simulada)
        saveTemplateBtn.addEventListener('click', function() {
            alert('Plantilla guardada correctamente');
        });
        
        // Escuchar cambios en los tipos de desafío para ajustar otros campos
        document.getElementById('challenge_type').addEventListener('change', function() {
            const challengeType = this.value;
            const timeAllocationSelect = document.getElementById('time_allocation');
            
            // Sugerir duración según el tipo de desafío seleccionado
            if (challengeType === 'actividad') {
                selectOption(timeAllocationSelect, 'una_sesion');
            } else if (challengeType === 'proyecto') {
                selectOption(timeAllocationSelect, 'semana');
            } else if (challengeType === 'hackathon') {
                selectOption(timeAllocationSelect, 'quincena');
            }
        });
        
        // Función auxiliar para seleccionar una opción en un select
        function selectOption(selectElement, value) {
            for (let i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value === value) {
                    selectElement.selectedIndex = i;
                    break;
                }
            }
        }
        
        // Manejar botones de plantillas guardadas
        document.querySelectorAll('#templates-container .btn').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Plantilla cargada en el formulario');
                // Aquí se implementaría la lógica para cargar los datos de la plantilla en el formulario
            });
        });
    });
</script>
@endsection 