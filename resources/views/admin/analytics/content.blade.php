@extends('layouts.admin')

@section('title', 'Análisis de Contenido')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
<style>
    .metric-card {
        border-radius: 10px;
        transition: all 0.3s;
    }
    
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .content-stat {
        text-align: center;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.08);
        transition: all 0.3s;
        background-color: #f8f9fc;
    }
    
    .content-stat:hover {
        transform: translateY(-5px);
    }
    
    .content-stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    
    .progress-bar-container {
        margin-bottom: 20px;
    }
    
    .progress-small {
        height: 8px;
        border-radius: 4px;
    }
    
    .metric-value {
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .metric-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: #666;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Mensaje de notificación -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Análisis de Contenido</h1>
        <div>
            <div class="dropdown d-inline-block mr-2">
                <button class="btn btn-sm btn-info shadow-sm dropdown-toggle" type="button" id="refreshDataDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-sync-alt fa-sm text-white-50 mr-1"></i> Refrescar Datos
                </button>
                <div class="dropdown-menu dropdown-menu-right animated--fade-in" aria-labelledby="refreshDataDropdown">
                    <h6 class="dropdown-header">Período a refrescar:</h6>
                    <a class="dropdown-item" href="{{ route('admin.analytics.content', ['refresh' => true]) }}">
                        <i class="fas fa-sync fa-sm fa-fw text-gray-500 mr-1"></i>
                        Solo datos de hoy
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.analytics.refresh', ['days' => 1]) }}">
                        <i class="fas fa-clock fa-sm fa-fw text-gray-500 mr-1"></i>
                        Último día
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.analytics.refresh', ['days' => 7]) }}">
                        <i class="fas fa-calendar-week fa-sm fa-fw text-gray-500 mr-1"></i>
                        Última semana
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.analytics.refresh', ['days' => 30]) }}">
                        <i class="fas fa-calendar-alt fa-sm fa-fw text-gray-500 mr-1"></i>
                        Último mes
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.analytics.refresh-purchases') }}">
                        <i class="fas fa-shopping-cart fa-sm fa-fw text-gray-500 mr-1"></i>
                        Actualizar compras
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.analytics.export', ['data_type' => 'content']) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50 mr-1"></i> Exportar Datos
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Tarjetas de resumen de desafíos -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 metric-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Desafíos Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $challengeStats['total'] }}</div>
                            <div class="text-xs mt-2">
                                <span class="text-success"><i class="fas fa-tasks mr-1"></i>{{ $challengeStats['in_progress'] }}</span> en progreso
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-code fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 metric-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tasa de Finalización</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $challengeStats['avg_completion_rate'] }}%</div>
                            <div class="text-xs mt-2">
                                <span class="text-info"><i class="fas fa-check-circle mr-1"></i>{{ $challengeStats['completed'] }}</span> completados
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 metric-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Hackathones Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $hackathonStats['active'] }}</div>
                            <div class="text-xs mt-2">
                                <span class="text-muted"><i class="fas fa-users mr-1"></i>{{ $hackathonStats['avg_participants'] }}</span> participantes promedio
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-laptop-code fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 metric-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Items en Tienda</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $storeStats['total_items'] }}</div>
                            <div class="text-xs mt-2">
                                <span class="text-success"><i class="fas fa-shopping-cart mr-1"></i>{{ $storeStats['total_purchases'] }}</span> compras totales
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis de Desafíos y Hackathones -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Rendimiento de Desafíos</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="challengePerformanceChart"></canvas>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="progress-bar-container">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Tasa de inicio</span>
                                    <span>{{ $challengeStats['start_rate'] }}%</span>
                                </div>
                                <div class="progress progress-small">
                                    <div class="progress-bar bg-info" style="width: {{ $challengeStats['start_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="progress-bar-container">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Tasa de finalización</span>
                                    <span>{{ $challengeStats['avg_completion_rate'] }}%</span>
                                </div>
                                <div class="progress progress-small">
                                    <div class="progress-bar bg-success" style="width: {{ $challengeStats['avg_completion_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Participación en Hackathones</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="hackathonParticipationChart"></canvas>
                    </div>
                    <div class="row mt-4 text-center">
                        <div class="col-md-4">
                            <div class="content-stat">
                                <i class="fas fa-laptop-code content-stat-icon text-primary"></i>
                                <div class="metric-value">{{ $hackathonStats['total'] }}</div>
                                <div class="metric-label">Total Hackathones</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="content-stat">
                                <i class="fas fa-users content-stat-icon text-success"></i>
                                <div class="metric-value">{{ $hackathonStats['avg_participants'] }}</div>
                                <div class="metric-label">Participantes Promedio</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="content-stat">
                                <i class="fas fa-trophy content-stat-icon text-warning"></i>
                                <div class="metric-value">{{ $hackathonStats['completed'] }}</div>
                                <div class="metric-label">Completados</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad en la Tienda y Métricas de Engagement -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Actividad en la Tienda</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="storeActivityChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body py-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold">Categoría más popular</h6>
                                        <h4 class="mb-0 text-primary">{{ ucfirst($storeStats['most_popular_category'] ?? 'N/A') }}</h4>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <h6 class="font-weight-bold">Ingresos totales</h6>
                                        <h4 class="mb-0 text-success">{{ number_format($storeStats['revenue']) }} <i class="fas fa-gem small"></i></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Métricas de Engagement</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="metric-value text-primary">{{ $challengeStats['avg_completion_rate'] }}%</div>
                                <div class="metric-label">Tasa Finalización Desafíos</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="metric-value text-success">{{ $storeStats['total_purchases'] }}</div>
                                <div class="metric-label">Compras Realizadas</div>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="font-weight-bold mb-3">Sugerencias para mejorar el engagement</h6>
                    <ul class="list-group">
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge badge-primary badge-pill mr-3">1</span>
                            Crear desafíos más cortos con recompensas inmediatas
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge badge-primary badge-pill mr-3">2</span>
                            Implementar un sistema de niveles más granular
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge badge-primary badge-pill mr-3">3</span>
                            Organizar hackathones temáticos con mayor frecuencia
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge badge-primary badge-pill mr-3">4</span>
                            Añadir más items a la tienda en la categoría {{ ucfirst($storeStats['most_popular_category'] ?? 'popular') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis de Tendencias y Oportunidades -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Oportunidades de Crecimiento</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card bg-gradient-primary text-white h-100">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-bold">Nuevos Tipos de Contenido</h5>
                                    <p class="card-text">
                                        Basado en el análisis actual, considera añadir:
                                    </p>
                                    <ul>
                                        <li>Desafíos en equipo</li>
                                        <li>Laboratorios virtuales interactivos</li>
                                        <li>Proyectos prácticos con mentorías</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div class="card bg-gradient-success text-white h-100">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-bold">Optimización de Contenido</h5>
                                    <p class="card-text">
                                        Para mejorar las tasas de finalización:
                                    </p>
                                    <ul>
                                        <li>Revisar desafíos con baja tasa de finalización</li>
                                        <li>Añadir más ayudas e indicaciones</li>
                                        <li>Implementar sistema de progreso por etapas</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div class="card bg-gradient-info text-white h-100">
                                <div class="card-body">
                                    <h5 class="card-title font-weight-bold">Estrategia de Hackathones</h5>
                                    <p class="card-text">
                                        Para mejorar participación:
                                    </p>
                                    <ul>
                                        <li>Organizar hackathones más cortos (48h)</li>
                                        <li>Establecer premios atractivos y visibles</li>
                                        <li>Promover la participación de mentores expertos</li>
                                    </ul>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de rendimiento de desafíos
        const challengePerformanceChart = new Chart(document.getElementById('challengePerformanceChart'), {
            type: 'pie',
            data: {
                labels: ['Completados', 'En progreso', 'No iniciados'],
                datasets: [{
                    data: [
                        {{ $challengeStats['completed'] }},
                        {{ $challengeStats['in_progress'] }},
                        {{ max(0, $challengeStats['total'] - $challengeStats['completed'] - $challengeStats['in_progress']) }}
                    ],
                    backgroundColor: ['#1cc88a', '#4e73df', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#2e59d9', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
        
        // Gráfico de participación en hackathones
        const hackathonParticipationChart = new Chart(document.getElementById('hackathonParticipationChart'), {
            type: 'bar',
            data: {
                labels: ['Activos', 'Completados', 'Participantes prom.'],
                datasets: [{
                    label: 'Hackathones',
                    data: [
                        {{ $hackathonStats['active'] }},
                        {{ $hackathonStats['completed'] }},
                        {{ $hackathonStats['avg_participants'] }}
                    ],
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(246, 194, 62, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)"
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Gráfico de actividad de la tienda
        // Simulamos datos para diferentes categorías
        const storeActivityChart = new Chart(document.getElementById('storeActivityChart'), {
            type: 'doughnut',
            data: {
                labels: ['Avatar', 'Badge', 'Rank', 'Skin', 'Special'],
                datasets: [{
                    data: [
                        {{ $storeStats['total_purchases'] * 0.3 }},  // Valores simulados
                        {{ $storeStats['total_purchases'] * 0.2 }},
                        {{ $storeStats['total_purchases'] * 0.15 }},
                        {{ $storeStats['total_purchases'] * 0.25 }},
                        {{ $storeStats['total_purchases'] * 0.1 }}
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#36b9cc', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#dda20a', '#2c9faf', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '60%'
            }
        });
    });
</script>
@endsection 