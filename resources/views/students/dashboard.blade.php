@extends('layouts.app')

@section('title', 'Panel del Estudiante')

@section('header', 'Panel del Estudiante')

@section('content')
    <!-- Estilos específicos para el dashboard de estudiantes -->
    <style>
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #4e73df;
            color: white;
            padding-top: 20px;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .content-section {
            display: none;
        }
        
        .content-section.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Card Styles */
        .progress-card {
            transition: transform 0.3s;
        }
        
        .progress-card:hover {
            transform: translateY(-5px);
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #5a5c69;
        }
        
        /* Resources Cards */
        .resource-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        /* Python Challenges Styles */
        #python-editor {
            font-size: 14px;
            border-radius: 0;
        }
        
        #python-console {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            line-height: 1.5;
            background-color: #2d2d2d;
            color: #f8f8f2;
        }
        
        .challenge-list {
            margin-top: 15px;
        }
        
        .challenge-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: 4px;
            border: 1px solid #e3e6f0;
            transition: all 0.2s;
        }
        
        .challenge-item.active {
            background-color: #e8f4ff;
            border-color: #4e73df;
            font-weight: 600;
        }
        
        .challenge-item.completed {
            background-color: #e8f8f0;
            border-color: #1cc88a;
        }
        
        .challenge-item.locked {
            background-color: #f8f9fc;
            color: #858796;
            border-color: #e3e6f0;
        }
        
        .challenge-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background-color: #4e73df;
            color: white;
            border-radius: 50%;
            margin-right: 10px;
            font-size: 12px;
            font-weight: 700;
        }
        
        .challenge-item.completed .challenge-number {
            background-color: #1cc88a;
        }
        
        .challenge-item.locked .challenge-number {
            background-color: #858796;
        }
        
        .challenge-name {
            flex-grow: 1;
        }
        
        .challenge-item.locked i {
            color: #858796;
            margin-left: 5px;
        }
        
        /* AI Tutor Chat Styles */
        .chat-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .chat-message {
            display: flex;
            margin-bottom: 10px;
        }
        
        .user-message {
            justify-content: flex-end;
        }
        
        .ai-message {
            justify-content: flex-start;
        }
        
        .chat-bubble {
            max-width: 80%;
            padding: 12px 15px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .user-message .chat-bubble {
            background-color: #4e73df;
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .ai-message .chat-bubble {
            background-color: #f0f2f5;
            color: #333;
            border-bottom-left-radius: 4px;
        }
        
        .chat-bubble p {
            margin-bottom: 0;
        }
        
        .chat-bubble p:not(:last-child) {
            margin-bottom: 8px;
        }
        
        .chat-bubble pre {
            margin: 10px 0;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
        }
        
        .ai-message .chat-bubble pre {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .user-message .chat-bubble pre {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Estilos para el perfil del usuario */
        .profile-stat {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .profile-stat:hover {
            transform: translateX(5px);
            background-color: #f8f9fc !important;
            border-left: 4px solid #4e73df;
        }
        
        .profile-stat i {
            font-size: 1.2rem;
            width: 25px;
            text-align: center;
        }
        
        .border-top-primary {
            transition: all 0.3s ease;
        }
        
        .border-top-primary:hover {
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
        }
    </style>

    <!-- Bienvenida -->
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h4">¡Bienvenido, {{ $student->first_name }}!</h2>
            <p>Continúa tu aprendizaje con Mitaí Code. 
            @if($studentProfile && $studentProfile->total_missions_completed > 0)
                Tu progreso total es <strong>{{ $studentProfile->total_progress }}%</strong>.
            @else
                Comienza tu primera misión para iniciar tu progreso.
            @endif
            </p>
            @if($studentProfile && $studentProfile->total_missions_completed > 0)
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($studentProfile && isset($studentProfile->total_progress) && $studentProfile->total_progress !== null) ? $studentProfile->total_progress : 0 }}%" 
                        aria-valuenow="{{ ($studentProfile && isset($studentProfile->total_progress) && $studentProfile->total_progress !== null) ? $studentProfile->total_progress : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Mi Perfil -->
    <div class="card mb-4 border-top-primary" style="border-top: 4px solid; border-image: linear-gradient(to right, #4e73df, #36b9cc) 1;">
        <div class="card-body">
            <h2 class="h5 mb-3"><i class="fas fa-user-circle text-primary me-2"></i>Mi Perfil</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="profile-stat bg-light p-3 mb-2 rounded">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-trophy text-warning me-2"></i>
                            <span>Nivel:</span>
                            <span class="fw-bold ms-auto">{{ $studentProfile ? $studentProfile->level : 1 }}</span>
                        </span>
                    </div>
                    
                    <div class="profile-stat bg-light p-3 mb-2 rounded">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-star text-primary me-2"></i>
                            <span>Puntos XP:</span>
                            <span class="fw-bold ms-auto">{{ $studentProfile ? $studentProfile->xp_points : 0 }}</span>
                        </span>
                    </div>
                    
                    <div class="profile-stat bg-light p-3 mb-2 rounded">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-tasks text-success me-2"></i>
                            <span>Misiones completadas:</span>
                            <span class="fw-bold ms-auto">{{ $studentProfile ? $studentProfile->total_missions_completed : 0 }}</span>
                        </span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="profile-stat bg-light p-3 mb-2 rounded">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-code text-info me-2"></i>
                            <span>Desafíos completados:</span>
                            <span class="fw-bold ms-auto">{{ isset($completedChallenges) ? $completedChallenges : 0 }}</span>
                        </span>
                    </div>
                    
                    @if(isset($streak) && $streak)
                    <div class="profile-stat bg-light p-3 mb-2 rounded">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-fire text-danger me-2"></i>
                            <span>Racha actual:</span>
                            <span class="fw-bold ms-auto">{{ $streak->current_streak }} días</span>
                        </span>
                    </div>
                    
                    <div class="profile-stat bg-light p-3 mb-2 rounded">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-award text-warning me-2"></i>
                            <span>Mejor racha:</span>
                            <span class="fw-bold ms-auto">{{ $streak->longest_streak }} días</span>
                        </span>
                    </div>
                    @else
                    <div class="profile-stat bg-light p-3 mb-2 rounded">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-fire text-danger me-2"></i>
                            <span>Racha actual:</span>
                            <span class="fw-bold ms-auto">0 días</span>
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('users.show', $student->user_id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-user me-1"></i> Ver perfil completo
                </a>
            </div>
        </div>
    </div>
    
    <!-- Pestañas del dashboard -->
    <ul class="nav nav-tabs mb-4" id="studentDashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" 
                type="button" role="tab" aria-controls="progress" aria-selected="true">Mi Progreso</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="missions-tab" data-bs-toggle="tab" data-bs-target="#missions" 
                type="button" role="tab" aria-controls="missions" aria-selected="false">Misiones</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="resources-tab" data-bs-toggle="tab" data-bs-target="#resources" 
                type="button" role="tab" aria-controls="resources" aria-selected="false">Recursos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="challenges-tab" data-bs-toggle="tab" data-bs-target="#challenges" 
                type="button" role="tab" aria-controls="challenges" aria-selected="false">Desafíos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="hackathons-tab" data-bs-toggle="tab" data-bs-target="#hackathons" 
                type="button" role="tab" aria-controls="hackathons" aria-selected="false">
                Hackathones <span class="badge bg-danger ms-1">Nuevo</span>
            </button>
        </li>
    </ul>
    
    <!-- Contenedor para efectos de confeti -->
    <div id="confetti-container" class="confetti-container" style="display: none;"></div>
    
    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="studentDashboardContent">
        <!-- Tab de Progreso -->
        <div class="tab-pane fade show active" id="progress" role="tabpanel" aria-labelledby="progress-tab">
            <!-- Tarjetas de Progreso -->
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="card progress-card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Prompt Aventura</h5>
                            <p class="card-text">Aprende a comunicarte con la IA</p>
                            <div class="progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="progress-info mt-2">
                                <span>2 de 3 misiones completadas</span>
                                <span>75%</span>
                            </div>
                            <a href="{{ route('missions.index') }}" class="btn btn-primary btn-sm mt-3">Continuar</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card progress-card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Mitaí Blocks</h5>
                            <p class="card-text">Programación con bloques</p>
                            <div class="progress">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="progress-info mt-2">
                                <span>2 de 5 misiones completadas</span>
                                <span>40%</span>
                            </div>
                            <a href="{{ route('missions.index') }}" class="btn btn-primary btn-sm mt-3">Continuar</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card progress-card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Pensamiento Computacional</h5>
                            <p class="card-text">Fundamentos teóricos</p>
                            <div class="progress">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="progress-info mt-2">
                                <span>1 de 5 misiones completadas</span>
                                <span>20%</span>
                            </div>
                            <a href="{{ route('missions.index') }}" class="btn btn-primary btn-sm mt-3">Continuar</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Logros Recientes -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-trophy me-1"></i>
                    Logros Recientes
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div class="badge-container">
                                <i class="fas fa-comment-dots text-warning" style="font-size: 3rem;"></i>
                                <h5 class="mt-2">¡Primer Prompt!</h5>
                                <p class="text-muted">Completaste tu primer prompt con éxito</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="badge-container">
                                <i class="fas fa-lightbulb text-info" style="font-size: 3rem;"></i>
                                <h5 class="mt-2">Maestro de la Claridad</h5>
                                <p class="text-muted">Dominaste el arte de crear prompts claros</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="badge-container">
                                <i class="fas fa-code text-success" style="font-size: 3rem;"></i>
                                <h5 class="mt-2">Programador Inicial</h5>
                                <p class="text-muted">Completaste tu primer programa en Mitaí Blocks</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab de Misiones -->
        <div class="tab-pane fade" id="missions" role="tabpanel" aria-labelledby="missions-tab">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar misiones..." id="searchMissions">
                        <button class="btn btn-outline-primary" type="button" id="searchMissionsBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end">
                        <select class="form-select me-2" style="max-width: 200px;" id="missionCategoryFilter">
                            <option value="all">Todas las categorías</option>
                            <option value="1">ChatGPT</option>
                            <option value="2">IA Generativa</option>
                            <option value="3">Ingeniería de Prompts</option>
                            <option value="4">Generación de Imágenes</option>
                            <option value="5">Ética en IA</option>
                            <option value="6">Fundamentos de IA</option>
                        </select>
                        <select class="form-select" style="max-width: 200px;" id="missionSortOrder">
                            <option value="newest">Más recientes</option>
                            <option value="oldest">Más antiguas</option>
                            <option value="points_high">Más puntos</option>
                            <option value="points_low">Menos puntos</option>
                            <option value="difficulty_asc">Dificultad (fácil→difícil)</option>
                            <option value="difficulty_desc">Dificultad (difícil→fácil)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Aquí se cargarán las misiones dinámicamente -->
            <div id="missions-container">
                <div class="alert alert-info">
                    <p>Las misiones se están cargando o aún no tienes ninguna disponible.</p>
                    <p>Visita la sección de <a href="{{ route('missions.index') }}" class="alert-link">Misiones</a> para ver todas las disponibles.</p>
                </div>
            </div>
        </div>
        
        <!-- Tab de Recursos -->
        <div class="tab-pane fade" id="resources" role="tabpanel" aria-labelledby="resources-tab">
            <!-- Tarjetas de Recursos -->
            <div class="row resource-cards">
                <div class="col-md-4">
                    <div class="card mb-4 resource-card">
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 160px;">
                            <i class="fas fa-robot text-primary" style="font-size: 60px;"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Prompt Aventura</h5>
                            <p class="card-text">Aprende a comunicarte con la IA a través de esta aventura interactiva de Prompt Engineering.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="badges">
                                    <span class="badge bg-primary">IA</span>
                                    <span class="badge bg-success">Interactivo</span>
                                </div>
                                <a href="#" class="btn btn-primary">Iniciar</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4 resource-card">
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 160px;">
                            <i class="fas fa-cubes text-info" style="font-size: 60px;"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Mitaí Blocks</h5>
                            <p class="card-text">Aprende a programar usando bloques visuales. Ideal para iniciar en el mundo de la programación.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="badges">
                                    <span class="badge bg-info">Programación</span>
                                    <span class="badge bg-warning">Principiante</span>
                                </div>
                                <a href="#" class="btn btn-primary">Iniciar</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4 resource-card">
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 160px;">
                            <i class="fas fa-brain text-danger" style="font-size: 60px;"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Pensamiento Computacional</h5>
                            <p class="card-text">Descubre los fundamentos del pensamiento computacional con actividades prácticas.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="badges">
                                    <span class="badge bg-secondary">Teoría</span>
                                    <span class="badge bg-danger">Fundamental</span>
                                </div>
                                <a href="#" class="btn btn-primary">Iniciar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab de Desafíos -->
        <div class="tab-pane fade" id="challenges" role="tabpanel" aria-labelledby="challenges-tab">
            <!-- Pestañas de tipos de desafíos -->
            <ul class="nav nav-tabs mb-4" id="challengesTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="python-challenges-tab" data-bs-toggle="tab" data-bs-target="#python-challenges" type="button" role="tab" aria-controls="python-challenges" aria-selected="true">
                        Python <span class="badge bg-primary ms-1">Nuevo</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="blocks-challenges-tab" data-bs-toggle="tab" data-bs-target="#blocks-challenges" type="button" role="tab" aria-controls="blocks-challenges" aria-selected="false">
                        Bloques
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ai-challenges-tab" data-bs-toggle="tab" data-bs-target="#ai-challenges" type="button" role="tab" aria-controls="ai-challenges" aria-selected="false">
                        Prompts IA
                    </button>
                </li>
            </ul>
            
            <!-- Contenido de las sub-pestañas -->
            <div class="tab-content" id="challengesTabContent">
                <!-- Python Challenges -->
                <div class="tab-pane fade show active" id="python-challenges" role="tabpanel" aria-labelledby="python-challenges-tab">
                    <!-- Selector de niveles -->
                    <div class="level-selector mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Selecciona un nivel:</h5>
                            <div class="btn-group">
                                <button class="btn btn-primary active" data-level="principiante">Principiante</button>
                                <button class="btn btn-outline-primary" data-level="intermedio">Intermedio</button>
                                <button class="btn btn-outline-primary" data-level="avanzado">Avanzado</button>
                            </div>
                        </div>
                        
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-1">
                            <span>Principiante: 10/10</span>
                            <span>Intermedio: 0/10</span>
                            <span>Avanzado: 0/10</span>
                        </div>
                    </div>
                    
                    <!-- Python Learning Environment -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Editor de Python</h5>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-2" id="runCode">
                                            <i class="fas fa-play me-1"></i> Ejecutar
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" id="submitCode">
                                            <i class="fas fa-check me-1"></i> Enviar
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="python-editor" style="height: 400px; width: 100%; border-bottom: 1px solid #e3e6f0;"></div>
                                    <div class="p-3">
                                        <h6>Consola</h6>
                                        <div id="python-console" class="bg-dark text-light p-3 rounded" style="height: 150px; overflow-y: auto; font-family: monospace;">
                                            &gt; Programa listo para ejecutar
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Instrucciones</h5>
                                </div>
                                <div class="card-body">
                                    <div id="challenge-instructions">
                                        <h6 id="challenge-title">Introducción a Python</h6>
                                        <div class="mb-3">
                                            <span class="badge bg-success">Principiante</span>
                                            <span class="badge bg-info">Fundamentos</span>
                                        </div>
                                        <p id="challenge-description">Bienvenido a tu primer desafío de Python. Vamos a empezar creando un programa simple.</p>
                                        <div class="alert alert-primary">
                                            <strong>Objetivo:</strong> Escribe un programa que imprima "¡Hola Mundo desde Python!" en la consola.
                                        </div>
                                        <div class="mb-3">
                                            <h6>Pistas:</h6>
                                            <ul>
                                                <li>Usa la función <code>print()</code> para mostrar texto en la consola</li>
                                                <li>El texto debe estar entre comillas</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-primary" id="getHint">
                                                <i class="fas fa-lightbulb me-1"></i> Obtener pista
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" id="askAI">
                                                <i class="fas fa-robot me-1"></i> Preguntar al tutor IA
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Challenge Progress -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Tu Progreso</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <span class="badge rounded-pill bg-primary p-2" style="font-size: 1.2rem;">1/10</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Desafío actual</h6>
                                            <small class="text-muted">Introducción a Python</small>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">10%</div>
                                    </div>
                                    
                                    <div class="challenge-list">
                                        <div class="challenge-item active">
                                            <span class="challenge-number">1</span>
                                            <span class="challenge-name">Introducción a Python</span>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">2</span>
                                            <span class="challenge-name">Variables y tipos de datos</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">3</span>
                                            <span class="challenge-name">Operadores y expresiones</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">4</span>
                                            <span class="challenge-name">Estructuras condicionales</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">5</span>
                                            <span class="challenge-name">Bucles</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">6</span>
                                            <span class="challenge-name">Listas</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">7</span>
                                            <span class="challenge-name">Funciones</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">8</span>
                                            <span class="challenge-name">Diccionarios</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">9</span>
                                            <span class="challenge-name">Manejo de errores</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">10</span>
                                            <span class="challenge-name">Proyecto final: Calculadora</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Tutor Modal -->
                    <div class="modal fade" id="aiTutorModal" tabindex="-1" aria-labelledby="aiTutorModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="aiTutorModalLabel">
                                        <i class="fas fa-robot me-2"></i> Tutor de Python IA
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="chat-container mb-3" style="height: 300px; overflow-y: auto;">
                                        <div class="chat-message ai-message">
                                            <div class="chat-bubble">
                                                <p>Hola, soy tu tutor de Python. ¿En qué puedo ayudarte con tu código?</p>
                                            </div>
                                        </div>
                                        <!-- Chat messages will be added here -->
                                    </div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="aiTutorInput" placeholder="Escribe tu pregunta aquí...">
                                        <button class="btn btn-primary" id="sendToAI">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                    <div class="form-text text-muted mt-2">
                                        Puedes preguntar sobre conceptos de Python, solicitar ayuda con errores o pedir explicaciones sobre el código.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Blocks Challenges -->
                <div class="tab-pane fade" id="blocks-challenges" role="tabpanel" aria-labelledby="blocks-challenges-tab">
                    <div class="card">
                        <div class="card-body">
                            <p>Los desafíos de programación por bloques se mostrarán aquí.</p>
                        </div>
                    </div>
                </div>
                
                <!-- AI Challenges -->
                <div class="tab-pane fade" id="ai-challenges" role="tabpanel" aria-labelledby="ai-challenges-tab">
                    <!-- Selector de niveles -->
                    <div class="level-selector mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Selecciona un nivel:</h5>
                            <div class="btn-group">
                                <button class="btn btn-primary active" data-level="principiante">Principiante</button>
                                <button class="btn btn-outline-primary" data-level="intermedio">Intermedio</button>
                                <button class="btn btn-outline-primary" data-level="avanzado">Avanzado</button>
                            </div>
                        </div>
                        
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-1">
                            <span>Principiante: 2/10</span>
                            <span>Intermedio: 0/10</span>
                            <span>Avanzado: 0/10</span>
                        </div>
                    </div>
                    
                    <!-- Prompt IA Learning Environment -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Editor de Prompts</h5>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary me-2" id="testPrompt">
                                            <i class="fas fa-play me-1"></i> Probar
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" id="submitPrompt">
                                            <i class="fas fa-check me-1"></i> Enviar
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-3">
                                        <textarea id="prompt-editor" class="form-control" rows="12" placeholder="Escribe tu prompt aquí..."></textarea>
                                    </div>
                                    <div class="p-3 border-top">
                                        <h6>Respuesta de la IA</h6>
                                        <div id="ai-response" class="bg-light p-3 rounded" style="height: 200px; overflow-y: auto;">
                                            <p class="text-muted">La respuesta de la IA aparecerá aquí...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Instrucciones</h5>
                                </div>
                                <div class="card-body">
                                    <div id="prompt-challenge-instructions">
                                        <h6 id="prompt-challenge-title">Fundamentos de Prompts</h6>
                                        <div class="mb-3">
                                            <span class="badge bg-success">Principiante</span>
                                            <span class="badge bg-info">Básico</span>
                                        </div>
                                        <p id="prompt-challenge-description">Aprende a escribir prompts claros y específicos para obtener mejores resultados de la IA.</p>
                                        <div class="alert alert-primary">
                                            <strong>Objetivo:</strong> Escribe un prompt que pida a la IA que se presente como un tutor de programación para niños.
                                        </div>
                                        <div class="mb-3">
                                            <h6>Pistas:</h6>
                                            <ul>
                                                <li>Especifica el tono amigable y simple</li>
                                                <li>Define la edad del público (niños de 8-12 años)</li>
                                                <li>Pide ejemplos concretos en el prompt</li>
                                            </ul>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-primary" id="getPromptHint">
                                                <i class="fas fa-lightbulb me-1"></i> Obtener pista
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" id="seeExamples">
                                                <i class="fas fa-list me-1"></i> Ver ejemplos
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Challenge Progress -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Tu Progreso</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <span class="badge rounded-pill bg-primary p-2" style="font-size: 1.2rem;">1/10</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Desafío actual</h6>
                                            <small class="text-muted">Fundamentos de Prompts</small>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">20%</div>
                                    </div>
                                    
                                    <div class="challenge-list">
                                        <div class="challenge-item active">
                                            <span class="challenge-number">1</span>
                                            <span class="challenge-name">Fundamentos de Prompts</span>
                                        </div>
                                        <div class="challenge-item completed">
                                            <span class="challenge-number">2</span>
                                            <span class="challenge-name">Roles y Personalidades</span>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">3</span>
                                            <span class="challenge-name">Instrucciones Claras</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">4</span>
                                            <span class="challenge-name">Formato de Salida</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">5</span>
                                            <span class="challenge-name">Context Building</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">6</span>
                                            <span class="challenge-name">Step-by-Step Thinking</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">7</span>
                                            <span class="challenge-name">Generación Creativa</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">8</span>
                                            <span class="challenge-name">Respuestas Específicas</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">9</span>
                                            <span class="challenge-name">Refinamiento Iterativo</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <div class="challenge-item locked">
                                            <span class="challenge-number">10</span>
                                            <span class="challenge-name">Prompt Avanzado: Storytelling</span>
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ejemplos Modal -->
                    <div class="modal fade" id="promptExamplesModal" tabindex="-1" aria-labelledby="promptExamplesModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="promptExamplesModalLabel">
                                        <i class="fas fa-lightbulb me-2"></i> Ejemplos de Prompts Efectivos
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3 border-success">
                                                <div class="card-header bg-success text-white">
                                                    Ejemplo Bueno
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text"><strong>Prompt:</strong> "Actúa como un tutor de programación amigable para niños de 10 años. Preséntate con un tono entusiasta y simple. Explica qué es un bucle 'for' usando una metáfora con juguetes y muestra un ejemplo de código muy sencillo con emojis."</p>
                                                    <hr>
                                                    <p class="card-text text-success"><strong>¿Por qué funciona?</strong> Específica el rol, la audiencia, el tono, el concepto a explicar, y el formato deseado.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-3 border-danger">
                                                <div class="card-header bg-danger text-white">
                                                    Ejemplo Malo
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text"><strong>Prompt:</strong> "Explica la programación a niños."</p>
                                                    <hr>
                                                    <p class="card-text text-danger"><strong>¿Por qué no funciona?</strong> Demasiado vago. No especifica el tono, el tema específico, ni cómo presentar la información.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-3">
                                        <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i> Elementos de un buen prompt:</h6>
                                        <ol>
                                            <li><strong>Rol/Contexto:</strong> Define quién debe "ser" la IA</li>
                                            <li><strong>Audiencia:</strong> Especifica para quién es el contenido</li>
                                            <li><strong>Tono/Estilo:</strong> Indica cómo debe comunicarse</li>
                                            <li><strong>Contenido específico:</strong> Detalla qué información quieres</li>
                                            <li><strong>Formato:</strong> Especifica cómo debe estructurarse la respuesta</li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab de Hackathones -->
        <div class="tab-pane fade" id="hackathons" role="tabpanel" aria-labelledby="hackathons-tab">
            <!-- Subtabs de Hackathones -->
            <ul class="nav nav-tabs mb-4" id="hackathonsStudentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="active-hackathons-tab" data-bs-toggle="tab" data-bs-target="#active-hackathons" type="button" role="tab" aria-controls="active-hackathons" aria-selected="true">
                        Activos <span class="badge bg-primary ms-1">2</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="my-teams-tab" data-bs-toggle="tab" data-bs-target="#my-teams" type="button" role="tab" aria-controls="my-teams" aria-selected="false">
                        Mis Equipos <span class="badge bg-success ms-1">1</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="past-hackathons-tab" data-bs-toggle="tab" data-bs-target="#past-hackathons" type="button" role="tab" aria-controls="past-hackathons" aria-selected="false">
                        Anteriores <span class="badge bg-secondary ms-1">1</span>
                    </button>
                </li>
            </ul>
            
            <!-- Contenido de las pestañas de Hackathones -->
            <div class="tab-content" id="hackathonsTabContent">
                <!-- Hackathones Activos -->
                <div class="tab-pane fade show active" id="active-hackathons" role="tabpanel" aria-labelledby="active-hackathons-tab">
                    <div class="row g-4">
                        <!-- Hackathon IA en Educación -->
                        <div class="col-lg-6">
                            <div class="card h-100 hackathon-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="m-0">IA en Educación</h5>
                                    <span class="badge bg-primary">
                                        En progreso - Ronda 1
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <span class="badge rounded-pill bg-primary me-2">Educación</span>
                                        <span class="badge rounded-pill bg-info me-2">IA</span>
                                        <span class="badge rounded-pill bg-warning me-2">Innovación</span>
                                    </div>
                                    
                                    <div class="hackathon-info mb-3">
                                        <div><i class="fas fa-calendar me-2"></i> <strong>Fecha:</strong> 15 Sep - 30 Oct, 2023</div>
                                        <div><i class="fas fa-users me-2"></i> <strong>Equipos:</strong> 8 equipos participando</div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-layer-group me-2"></i> <strong>Progreso:</strong>
                                            <div class="progress ms-2" style="height: 8px; width: 150px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                     role="progressbar" 
                                                     style="width: 33%" 
                                                     aria-valuenow="33" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <span class="ms-2">Ronda 1 de 3</span>
                                        </div>
                                    </div>
                                    
                                    <p class="card-text">Desarrolla soluciones innovadoras que utilicen IA para mejorar la experiencia educativa. Enfócate en personalización, accesibilidad o herramientas para docentes.</p>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-danger"><i class="fas fa-info-circle me-1"></i> No estás participando</span>
                                        </div>
                                        <button id="joinEducationHackathon" class="btn btn-success">
                                            <i class="fas fa-plus me-1"></i> Unirse o crear equipo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hackathon Soluciones contra el Cambio Climático -->
                        <div class="col-lg-6">
                            <div class="card h-100 hackathon-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="m-0">Soluciones contra el Cambio Climático</h5>
                                    <span class="badge bg-success">
                                        En progreso - Ronda 2
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <span class="badge rounded-pill bg-success me-2">Medio Ambiente</span>
                                        <span class="badge rounded-pill bg-info me-2">Sostenibilidad</span>
                                        <span class="badge rounded-pill bg-primary me-2">Tecnología Verde</span>
                                    </div>
                                    
                                    <div class="hackathon-info mb-3">
                                        <div><i class="fas fa-calendar me-2"></i> <strong>Fecha:</strong> 1 Sep - 15 Oct, 2023</div>
                                        <div><i class="fas fa-users me-2"></i> <strong>Equipos:</strong> 12 equipos participando</div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-layer-group me-2"></i> <strong>Progreso:</strong>
                                            <div class="progress ms-2" style="height: 8px; width: 150px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                                     role="progressbar" 
                                                     style="width: 66%" 
                                                     aria-valuenow="66" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <span class="ms-2">Ronda 2 de 3</span>
                                        </div>
                                    </div>
                                    
                                    <p class="card-text">Crea aplicaciones o soluciones tecnológicas que ayuden a combatir el cambio climático, reducir la huella de carbono o promover prácticas sostenibles.</p>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-success"><i class="fas fa-check-circle me-1"></i> Ya estás participando</span>
                                        </div>
                                        <button id="viewClimateHackathon" class="btn btn-primary">
                                            <i class="fas fa-eye me-1"></i> Ver mi equipo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mis Equipos -->
                <div class="tab-pane fade" id="my-teams" role="tabpanel" aria-labelledby="my-teams-tab">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">EcoCoders</h5>
                            <span class="badge bg-success">Hackathon: Soluciones contra el Cambio Climático</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-4 mb-md-0">
                                    <h6 class="fw-bold">Miembros del equipo</h6>
                                    <ul class="list-group mb-3">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('img/avatars/default.png') }}" class="rounded-circle me-2" width="30" height="30" alt="Avatar">
                                                <span>Ana García (Tú)</span>
                                            </div>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('img/avatars/default.png') }}" class="rounded-circle me-2" width="30" height="30" alt="Avatar">
                                                <span>Carlos Martínez</span>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">Líder</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('img/avatars/default.png') }}" class="rounded-circle me-2" width="30" height="30" alt="Avatar">
                                                <span>Sofía Torres</span>
                                            </div>
                                        </li>
                                    </ul>
                                    <button class="btn btn-sm btn-outline-primary w-100">
                                        <i class="fas fa-comment-dots me-1"></i> Chat de equipo
                                    </button>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0">Ronda actual: Desarrollo de Prototipo</h6>
                                        <div>
                                            <span class="badge bg-warning">
                                                Tiempo restante: 5 días
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="objectives-card mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Objetivos de la ronda</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="mb-0">
                                                    <li>Desarrollar un prototipo funcional de la solución</li>
                                                    <li>Implementar al menos 3 características clave</li>
                                                    <li>Documentar el proceso de desarrollo</li>
                                                    <li>Preparar una demostración del prototipo</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="deliverables-card mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Entregables</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="mb-0">
                                                    <li>Código fuente del prototipo</li>
                                                    <li>Video de demostración (máx. 3 minutos)</li>
                                                    <li>Documento de avance del proyecto</li>
                                                    <li>Presentación de diapositivas para la evaluación</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-column flex-sm-row justify-content-between">
                                        <button class="btn btn-primary mb-2 mb-sm-0">
                                            <i class="fas fa-upload me-1"></i> Subir entregables
                                        </button>
                                        <a href="#" target="_blank" class="btn btn-outline-primary">
                                            <i class="fas fa-code-branch me-1"></i> Ver repositorio del proyecto
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hackathones Anteriores -->
                <div class="tab-pane fade" id="past-hackathons" role="tabpanel" aria-labelledby="past-hackathons-tab">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Inteligencia Artificial para la Salud</h5>
                            <span class="badge bg-secondary">Finalizado</span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row">
                                <div class="me-md-4 mb-3 mb-md-0 text-center">
                                    <div class="p-3 mb-2" style="background: linear-gradient(45deg, #c0c0c0, #e0e0e0); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-medal text-warning fa-3x"></i>
                                    </div>
                                    <h5>2º Puesto</h5>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="hackathon-info mb-3">
                                        <div><i class="fas fa-calendar me-2"></i> <strong>Fecha:</strong> 1 Jun - 30 Jun, 2023</div>
                                        <div>
                                            <i class="fas fa-users me-2"></i> <strong>Equipo:</strong> 
                                            HealthTech Heroes
                                            (Ana, Lucas, María)
                                        </div>
                                        <div>
                                            <i class="fas fa-project-diagram me-2"></i> <strong>Proyecto:</strong> 
                                            MediAssist - Asistente de diagnóstico con IA
                                        </div>
                                    </div>
                                    
                                    <p class="mb-3">
                                        Desarrollamos un asistente de diagnóstico basado en IA que ayuda a los médicos a identificar patrones en imágenes médicas y datos de pacientes para sugerir posibles diagnósticos.
                                    </p>
                                    
                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-alt me-1"></i> Ver proyecto
                                        </button>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-certificate me-1"></i> Ver certificado
                                        </button>
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-code me-1"></i> Ver código
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal para unirse o crear equipo -->
            <div class="modal fade" id="teamJoinModal" tabindex="-1" aria-labelledby="teamJoinModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="teamJoinModalLabel">Participar en Hackathon: IA en Educación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs" id="teamModalTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="join-team-tab" data-bs-toggle="tab" data-bs-target="#join-team" type="button" role="tab" aria-controls="join-team" aria-selected="true">Unirse a un equipo</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="create-team-tab" data-bs-toggle="tab" data-bs-target="#create-team" type="button" role="tab" aria-controls="create-team" aria-selected="false">Crear equipo nuevo</button>
                                </li>
                            </ul>
                            <div class="tab-content p-3" id="teamModalTabContent">
                                <div class="tab-pane fade show active" id="join-team" role="tabpanel" aria-labelledby="join-team-tab">
                                    <p class="text-muted">Selecciona un equipo para unirte:</p>
                                    <div class="list-group mb-3">
                                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-team-id="1">
                                            <div>
                                                <h6 class="mb-1">Education IA Team</h6>
                                                <small class="text-muted">3 de 4 miembros · Creado por Mario González</small>
                                            </div>
                                            <button class="btn btn-primary btn-sm">Unirse</button>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-team-id="2">
                                            <div>
                                                <h6 class="mb-1">Innovadores Educativos</h6>
                                                <small class="text-muted">2 de 4 miembros · Creado por Laura Pérez</small>
                                            </div>
                                            <button class="btn btn-primary btn-sm">Unirse</button>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-team-id="3">
                                            <div>
                                                <h6 class="mb-1">EdTech Solutions</h6>
                                                <small class="text-muted">2 de 4 miembros · Creado por Daniel Ruiz</small>
                                            </div>
                                            <button class="btn btn-primary btn-sm">Unirse</button>
                                        </a>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="create-team" role="tabpanel" aria-labelledby="create-team-tab">
                                    <form>
                                        <div class="mb-3">
                                            <label for="teamName" class="form-label">Nombre del equipo</label>
                                            <input type="text" class="form-control" id="teamName" placeholder="Ingresa un nombre para tu equipo">
                                        </div>
                                        <div class="mb-3">
                                            <label for="teamDescription" class="form-label">Descripción del proyecto (idea inicial)</label>
                                            <textarea class="form-control" id="teamDescription" rows="3" placeholder="Describe brevemente tu idea para el hackathon"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Invitar compañeros</label>
                                            <div class="input-group">
                                                <select class="form-select" id="teamMemberSelect">
                                                    <option selected>Seleccionar compañero</option>
                                                    <option value="user1">Carlos Vega</option>
                                                    <option value="user2">Daniela Morales</option>
                                                    <option value="user3">Roberto Mendoza</option>
                                                    <option value="user4">Lucía Torres</option>
                                                    <option value="user5">Gabriel Rojas</option>
                                                </select>
                                                <button class="btn btn-outline-primary" type="button">Añadir</button>
                                            </div>
                                            <small class="form-text text-muted">Puedes invitar hasta 3 compañeros más.</small>
                                        </div>
                                        <div id="invitedTeammates" class="mb-3">
                                            <!-- Aquí se mostrarán los compañeros invitados -->
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="confirmTeamAction">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts específicos para el dashboard de estudiantes -->
    <script src="{{ asset('js/student-dashboard.js') }}"></script>
    
    <!-- Scripts para los desafíos de Python -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/mode-python.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/theme-monokai.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/python-challenges.js') }}"></script>
    
    <!-- Estilos para certificados y animaciones -->
    <link rel="stylesheet" href="{{ asset('css/certificates.css') }}">
    
    <!-- Modal para certificado (se mostrará mediante JavaScript) -->
    <div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateModalLabel">
                        <i class="fas fa-award me-2"></i> Certificado de Logro
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="certificate">
                        <div class="certificate-header">
                            <img src="{{ asset('assets/images/mitai-logo-128x128.svg') }}" alt="MitaiCode" width="50">
                            <h2>MitaiCode Academy</h2>
                        </div>
                        <div class="certificate-body">
                            <p>Este certificado se otorga a</p>
                            <h3>{{ $student->first_name }} {{ $student->last_name }}</h3>
                            <p>Por completar exitosamente todos los desafíos de</p>
                            <h4 id="certificate-level">Python - Nivel Principiante</h4>
                            <p>Fecha: <span id="certificate-date">{{ date('d/m/Y') }}</span></p>
                        </div>
                        <div class="certificate-footer">
                            <div class="signature">
                                <div style="height: 40px; margin-bottom: 5px; font-family: cursive;">Director</div>
                                <p>Director Académico</p>
                            </div>
                            <div class="signature">
                                <div style="height: 40px; margin-bottom: 5px; font-family: cursive;">Instructor</div>
                                <p>Instructor Principal</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="downloadCertificate">Descargar Certificado</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/python-challenges.js') }}"></script>
<script src="{{ asset('js/hackathons.js') }}"></script>
@endsection 