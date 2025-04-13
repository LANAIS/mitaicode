@extends('layouts.app')

@section('title', 'Generador de Variantes de Desafíos')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.ai_assistant.index') }}">Asistente de IA</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Generador de Variantes</li>
                </ol>
            </nav>
            <h1 class="h2 mb-3">Generador de Variantes de Desafíos</h1>
            <p class="text-muted">Crea variantes de tus desafíos existentes para adaptarlos a diferentes contextos y niveles.</p>
        </div>
    </div>

    <div class="row">
        <!-- Formulario de generación -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="variant-generator-form" method="POST" action="{{ route('teacher.ai_assistant.generate_variant') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="original_challenge" class="form-label">Desafío original</label>
                            <textarea class="form-control" id="original_challenge" name="original_challenge" rows="6" placeholder="Pega aquí el texto completo de tu desafío original" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="variation_type" class="form-label">Tipo de variación</label>
                            <select class="form-select" id="variation_type" name="variation_type" required>
                                <option value="" selected disabled>Selecciona un tipo de variación</option>
                                <option value="dificultad">Cambiar nivel de dificultad</option>
                                <option value="contexto">Cambiar contexto o temática</option>
                                <option value="enfoque">Cambiar enfoque pedagógico</option>
                                <option value="herramientas">Cambiar herramientas o recursos</option>
                                <option value="formato">Cambiar formato de entrega</option>
                            </select>
                        </div>
                        
                        <div id="dificultad-options" class="variation-options mb-3 d-none">
                            <label for="difficulty_level" class="form-label">Nuevo nivel de dificultad</label>
                            <select class="form-select" id="difficulty_level" name="difficulty_level">
                                <option value="simplificar">Simplificar (más fácil)</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                                <option value="experto">Experto</option>
                            </select>
                        </div>
                        
                        <div id="contexto-options" class="variation-options mb-3 d-none">
                            <label for="new_context" class="form-label">Nuevo contexto o temática</label>
                            <input type="text" class="form-control" id="new_context" name="new_context" placeholder="Ej: videojuegos, sostenibilidad, ciencias sociales, etc.">
                        </div>
                        
                        <div id="enfoque-options" class="variation-options mb-3 d-none">
                            <label for="new_approach" class="form-label">Nuevo enfoque pedagógico</label>
                            <select class="form-select" id="new_approach" name="new_approach">
                                <option value="colaborativo">Aprendizaje colaborativo</option>
                                <option value="basado_proyecto">Aprendizaje basado en proyectos</option>
                                <option value="basado_problema">Aprendizaje basado en problemas</option>
                                <option value="indagacion">Aprendizaje por indagación</option>
                                <option value="gamificado">Gamificación</option>
                            </select>
                        </div>
                        
                        <div id="herramientas-options" class="variation-options mb-3 d-none">
                            <label for="new_tools" class="form-label">Nuevas herramientas o recursos</label>
                            <select class="form-select" id="new_tools" name="new_tools">
                                <option value="python">Python</option>
                                <option value="scratch">Scratch</option>
                                <option value="unplugged">Actividades desconectadas (unplugged)</option>
                                <option value="ia_tools">Herramientas de IA</option>
                                <option value="robotica">Robótica</option>
                            </select>
                        </div>
                        
                        <div id="formato-options" class="variation-options mb-3 d-none">
                            <label for="new_format" class="form-label">Nuevo formato de entrega</label>
                            <select class="form-select" id="new_format" name="new_format">
                                <option value="informe">Informe escrito</option>
                                <option value="presentacion">Presentación</option>
                                <option value="video">Video</option>
                                <option value="demo">Demostración en vivo</option>
                                <option value="producto">Producto digital funcional</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="additional_instructions" class="form-label">Instrucciones adicionales (opcional)</label>
                            <textarea class="form-control" id="additional_instructions" name="additional_instructions" rows="2" placeholder="Cualquier indicación específica para la variante"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="num_variations" class="form-label">Número de variantes a generar</label>
                            <select class="form-select" id="num_variations" name="num_variations">
                                <option value="1" selected>1 variante</option>
                                <option value="2">2 variantes</option>
                                <option value="3">3 variantes</option>
                            </select>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" id="generate-btn" class="btn btn-primary">
                                <i class="fas fa-random me-2"></i> Generar Variantes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Área de resultados -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="h5 mb-3">Variantes generadas</h4>
                    
                    <div id="loading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Generando variantes del desafío...</p>
                    </div>
                    
                    <div id="result-container" class="d-none">
                        <div class="nav nav-tabs mb-3" id="variant-tabs" role="tablist">
                            <button class="nav-link active" id="variant-1-tab" data-bs-toggle="tab" data-bs-target="#variant-1" type="button" role="tab" aria-controls="variant-1" aria-selected="true">Variante 1</button>
                            <button class="nav-link d-none" id="variant-2-tab" data-bs-toggle="tab" data-bs-target="#variant-2" type="button" role="tab" aria-controls="variant-2" aria-selected="false">Variante 2</button>
                            <button class="nav-link d-none" id="variant-3-tab" data-bs-toggle="tab" data-bs-target="#variant-3" type="button" role="tab" aria-controls="variant-3" aria-selected="false">Variante 3</button>
                        </div>
                        
                        <div class="tab-content" id="variant-content">
                            <div class="tab-pane fade show active" id="variant-1" role="tabpanel" aria-labelledby="variant-1-tab">
                                <div class="variant-result mb-4"></div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-outline-secondary btn-sm me-2 copy-btn" data-target="1">
                                        <i class="fas fa-copy me-1"></i> Copiar
                                    </button>
                                    <button class="btn btn-outline-success btn-sm save-btn" data-target="1">
                                        <i class="fas fa-save me-1"></i> Guardar
                                    </button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="variant-2" role="tabpanel" aria-labelledby="variant-2-tab">
                                <div class="variant-result mb-4"></div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-outline-secondary btn-sm me-2 copy-btn" data-target="2">
                                        <i class="fas fa-copy me-1"></i> Copiar
                                    </button>
                                    <button class="btn btn-outline-success btn-sm save-btn" data-target="2">
                                        <i class="fas fa-save me-1"></i> Guardar
                                    </button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="variant-3" role="tabpanel" aria-labelledby="variant-3-tab">
                                <div class="variant-result mb-4"></div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-outline-secondary btn-sm me-2 copy-btn" data-target="3">
                                        <i class="fas fa-copy me-1"></i> Copiar
                                    </button>
                                    <button class="btn btn-outline-success btn-sm save-btn" data-target="3">
                                        <i class="fas fa-save me-1"></i> Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button id="regenerate-btn" class="btn btn-outline-primary">
                                <i class="fas fa-redo me-2"></i> Regenerar
                            </button>
                        </div>
                    </div>
                    
                    <div id="empty-state" class="text-center py-5">
                        <img src="{{ asset('images/variant-icon.svg') }}" alt="Generador de variantes" class="mb-4" style="max-width: 120px; opacity: 0.7;">
                        <h5>Aún no has generado ninguna variante</h5>
                        <p class="text-muted">Completa el formulario y haz clic en "Generar Variantes" para ver los resultados aquí.</p>
                    </div>
                </div>
            </div>
            
            <!-- Consejos de uso -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="h6 mb-3"><i class="fas fa-lightbulb text-warning me-2"></i> Consejos para generar variantes efectivas</h5>
                    <ul class="text-muted small mb-0">
                        <li class="mb-2">Incluye el texto completo del desafío para mejores resultados.</li>
                        <li class="mb-2">Para cambiar el nivel de dificultad, especifica claramente la dirección (más fácil o más difícil).</li>
                        <li class="mb-2">Si deseas cambiar el contexto, sé específico con la nueva temática (ej. "videojuegos", "medicina").</li>
                        <li>En las instrucciones adicionales puedes especificar aspectos particulares que quieres conservar de la versión original.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('variant-generator-form');
        const generateBtn = document.getElementById('generate-btn');
        const regenerateBtn = document.getElementById('regenerate-btn');
        const variationTypeSelect = document.getElementById('variation_type');
        const numVariationsSelect = document.getElementById('num_variations');
        const loadingElement = document.getElementById('loading');
        const resultContainer = document.getElementById('result-container');
        const emptyState = document.getElementById('empty-state');
        
        // Variables para almacenar las variantes generadas
        let generatedVariants = [];
        let selectedVariantIndex = null;
        
        // Mostrar/ocultar opciones según el tipo de variación seleccionada
        variationTypeSelect.addEventListener('change', function() {
            // Ocultar todas las opciones de variación
            document.querySelectorAll('.variation-options').forEach(el => {
                el.classList.add('d-none');
            });
            
            // Mostrar la opción correspondiente
            const selectedOption = this.value;
            if (selectedOption) {
                const optionsDiv = document.getElementById(selectedOption + '-options');
                if (optionsDiv) {
                    optionsDiv.classList.remove('d-none');
                }
            }
        });
        
        // Actualizar la interfaz según el número de variantes seleccionadas
        numVariationsSelect.addEventListener('change', function() {
            const numVariations = parseInt(this.value);
            
            // Ocultar/mostrar pestañas según el número seleccionado
            for (let i = 1; i <= 3; i++) {
                const tab = document.getElementById(`variant-${i}-tab`);
                if (i <= numVariations) {
                    tab.classList.remove('d-none');
                } else {
                    tab.classList.add('d-none');
                }
            }
        });
        
        // Simular la generación de variantes para el formulario de ejemplo
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar indicador de carga
            loadingElement.classList.remove('d-none');
            emptyState.classList.add('d-none');
            resultContainer.classList.add('d-none');
            
            // Simulación de llamada a la API (reemplazar con llamada real)
            setTimeout(function() {
                // Ocultar carga y mostrar resultados
                loadingElement.classList.add('d-none');
                resultContainer.classList.remove('d-none');
                
                // Aquí se procesarían los resultados de la API
                // Por ahora, usamos datos de ejemplo
                const variantType = variationTypeSelect.value;
                const numVariations = parseInt(numVariationsSelect.value);
                
                // Simular variantes generadas basadas en la selección
                generatedVariants = [];
                for (let i = 1; i <= numVariations; i++) {
                    generatedVariants.push({
                        title: `Variante ${i} de tipo ${variantType}`,
                        description: `Esta es una descripción de ejemplo para la variante ${i}. Se ha modificado según el tipo de variación seleccionado: "${variantType}".`,
                        instructions: `Instrucciones detalladas para la variante ${i}. Estas instrucciones son diferentes a las originales y se enfocan en el tipo de variación "${variantType}".`,
                        hints: `Aquí hay algunas pistas para ayudar con la variante ${i}.`,
                        materials: `Materiales necesarios para la variante ${i}.`,
                        objectives: `Objetivos de aprendizaje para la variante ${i}.`,
                        starter_code: variantType === 'dificultad' ? `# Código inicial para la variante ${i}\ndef resolver_problema():\n    # Tu código aquí\n    pass` : '',
                        solution_code: variantType === 'dificultad' ? `# Solución para la variante ${i}\ndef resolver_problema():\n    # Implementación\n    return "Solución"` : '',
                        example_prompt: variantType === 'herramientas' && document.getElementById('new_tools').value === 'ia_tools' ? `Ejemplo de prompt para IA para la variante ${i}` : ''
                    });
                }
                
                // Actualizar la interfaz con las variantes generadas
                updateVariantDisplay();
            }, 2000);
        });
        
        // Actualizar la visualización de las variantes
        function updateVariantDisplay() {
            const numVariations = generatedVariants.length;
            
            // Actualizar el contenido de cada pestaña de variante
            for (let i = 0; i < numVariations; i++) {
                const variant = generatedVariants[i];
                const variantNumber = i + 1;
                const resultElement = document.querySelector(`#variant-${variantNumber} .variant-result`);
                
                // Construir HTML para la variante
                let variantHtml = `
                    <h5>${variant.title}</h5>
                    <p>${variant.description}</p>
                    <h6>Instrucciones:</h6>
                    <p>${variant.instructions}</p>
                `;
                
                if (variant.hints) {
                    variantHtml += `<h6>Pistas:</h6><p>${variant.hints}</p>`;
                }
                
                if (variant.starter_code) {
                    variantHtml += `
                        <h6>Código inicial:</h6>
                        <pre class="bg-light p-3 rounded"><code>${variant.starter_code}</code></pre>
                    `;
                }
                
                if (variant.example_prompt) {
                    variantHtml += `
                        <h6>Ejemplo de prompt:</h6>
                        <pre class="bg-light p-3 rounded"><code>${variant.example_prompt}</code></pre>
                    `;
                }
                
                resultElement.innerHTML = variantHtml;
            }
            
            // Activar botones después de generar variantes
            document.querySelectorAll('.copy-btn, .save-btn').forEach(button => {
                button.disabled = false;
            });
        }
        
        // Configurar botones de copiar
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', function() {
                const targetIndex = parseInt(this.getAttribute('data-target')) - 1;
                if (generatedVariants[targetIndex]) {
                    // Convertir la variante a texto
                    const variant = generatedVariants[targetIndex];
                    const textToCopy = `${variant.title}\n\n${variant.description}\n\nInstrucciones:\n${variant.instructions}\n\nPistas:\n${variant.hints}`;
                    
                    // Copiar al portapapeles
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        // Feedback visual temporal
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check me-1"></i> Copiado';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    });
                }
            });
        });
        
        // Configurar botones de guardar
        document.querySelectorAll('.save-btn').forEach(button => {
            button.addEventListener('click', function() {
                const targetIndex = parseInt(this.getAttribute('data-target')) - 1;
                if (generatedVariants[targetIndex]) {
                    selectedVariantIndex = targetIndex;
                    const variant = generatedVariants[targetIndex];
                    
                    // Cambiar el estado del botón
                    document.querySelectorAll('.save-btn').forEach(btn => {
                        btn.innerHTML = '<i class="fas fa-save me-1"></i> Guardar';
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-outline-success');
                    });
                    
                    this.innerHTML = '<i class="fas fa-check me-1"></i> Seleccionado';
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-success');
                    
                    // Enviar mensaje al padre si estamos en un iframe
                    if (window.parent && window.parent !== window) {
                        window.parent.postMessage({
                            type: 'variant-selected',
                            variant: {
                                title: variant.title,
                                description: variant.description,
                                instructions: variant.instructions,
                                hints: variant.hints,
                                materials: variant.materials,
                                objectives: variant.objectives,
                                starter_code: variant.starter_code,
                                solution_code: variant.solution_code,
                                example_prompt: variant.example_prompt
                            }
                        }, '*');
                        
                        // Guardar en localStorage como respaldo
                        localStorage.setItem('selectedVariant', JSON.stringify(variant));
                        
                        // Mostrar alerta de confirmación
                        alert('Variante seleccionada. Se aplicará automáticamente al formulario principal.');
                    } else {
                        // Si no estamos en un iframe, simplemente guardar en localStorage
                        localStorage.setItem('selectedVariant', JSON.stringify(variant));
                        alert('Variante guardada correctamente.');
                    }
                }
            });
        });
        
        // Botón de regenerar
        regenerateBtn.addEventListener('click', function() {
            // Simular nueva generación (en producción, sería una nueva llamada a la API)
            loadingElement.classList.remove('d-none');
            resultContainer.classList.add('d-none');
            
            setTimeout(function() {
                loadingElement.classList.add('d-none');
                resultContainer.classList.remove('d-none');
                
                // Simular nuevas variantes con pequeñas diferencias
                for (let i = 0; i < generatedVariants.length; i++) {
                    generatedVariants[i].title += ' (regenerada)';
                    generatedVariants[i].description = 'Esta variante ha sido regenerada con nuevos parámetros.';
                }
                
                updateVariantDisplay();
            }, 1500);
        });
    });
</script>
@endsection 