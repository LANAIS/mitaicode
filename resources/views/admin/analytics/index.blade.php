@extends('layouts.admin')

@section('title', 'Dashboard de Analítica')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
<style>
    .stats-card {
        transition: all 0.3s;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .stats-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    
    .stats-card.primary {
        border-left: 4px solid #4e73df;
    }
    
    .stats-card.success {
        border-left: 4px solid #1cc88a;
    }
    
    .stats-card.info {
        border-left: 4px solid #36b9cc;
    }
    
    .stats-card.warning {
        border-left: 4px solid #f6c23e;
    }
    
    .stats-card.danger {
        border-left: 4px solid #e74a3b;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard de Analítica</h1>
        <div>
            <a href="{{ route('admin.analytics.export') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50 mr-1"></i> Exportar Datos
            </a>
            <div class="btn-group ml-2">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar fa-sm mr-1"></i> Período
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item period-filter" href="#" data-days="7">Últimos 7 días</a>
                    <a class="dropdown-item period-filter active" href="#" data-days="30">Últimos 30 días</a>
                    <a class="dropdown-item period-filter" href="#" data-days="90">Últimos 3 meses</a>
                    <a class="dropdown-item period-filter" href="#" data-days="365">Último año</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 py-2 stats-card primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Usuarios Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUsers) }}</div>
                            <div class="text-xs text-success mt-2">
                                <i class="fas fa-arrow-up mr-1"></i>{{ $newUsersLast30Days }} nuevos en 30 días
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users text-gray-300 stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 py-2 stats-card success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Usuarios Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeUsersLast30Days) }}</div>
                            <div class="text-xs text-muted mt-2">
                                <i class="fas fa-clock mr-1"></i>Últimos 30 días
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check text-gray-300 stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 py-2 stats-card info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasa Participación</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ round(($challengeParticipations / $totalStudents) * 100, 1) }}%</div>
                            <div class="text-xs text-muted mt-2">
                                <i class="fas fa-tasks mr-1"></i>{{ number_format($challengeParticipations) }} participantes
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-award text-gray-300 stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 py-2 stats-card warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Transacciones Tienda</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalTransactions) }}</div>
                            <div class="text-xs text-muted mt-2">
                                <i class="fas fa-shopping-cart mr-1"></i>{{ number_format($totalItemsSold) }} items vendidos
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gem text-gray-300 stats-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución de usuarios -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución de Usuarios</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="userDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución de Actividad</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activityDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Tendencia -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tendencias de Crecimiento</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="growthTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlaces Rápidos a Informes Detallados -->
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-users fa-3x mb-2"></i>
                        <h5 class="text-white mb-2">Análisis de Usuarios</h5>
                        <p class="mb-4">Estadísticas detalladas sobre usuarios, comportamiento y retención.</p>
                        <a href="{{ route('admin.analytics.users') }}" class="btn btn-light btn-sm">Ver Informe</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-tasks fa-3x mb-2"></i>
                        <h5 class="text-white mb-2">Análisis de Contenido</h5>
                        <p class="mb-4">Datos sobre desafíos, hackathones y engagement.</p>
                        <a href="{{ route('admin.analytics.content') }}" class="btn btn-light btn-sm">Ver Informe</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-chart-line fa-3x mb-2"></i>
                        <h5 class="text-white mb-2">Exportar Datos</h5>
                        <p class="mb-4">Descarga los datos para análisis avanzados en Excel o CSV.</p>
                        <a href="{{ route('admin.analytics.export') }}" class="btn btn-light btn-sm">Exportar Ahora</a>
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
    // Datos para los gráficos
    const userDistributionData = {
        labels: ['Estudiantes', 'Profesores', 'Administradores'],
        datasets: [{
            data: [
                {{ $totalStudents }}, 
                {{ $totalTeachers }}, 
                {{ $totalUsers - $totalStudents - $totalTeachers }}
            ],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    };
    
    const activityDistributionData = {
        labels: ['Activos (30d)', 'Inactivos (30d)'],
        datasets: [{
            data: [
                {{ $activeUsersLast30Days }}, 
                {{ $totalUsers - $activeUsersLast30Days }}
            ],
            backgroundColor: ['#1cc88a', '#e74a3b'],
            hoverBackgroundColor: ['#17a673', '#be2617'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    };
    
    // Convertir los datos de tendencias para el gráfico
    const dates = @json(array_column($userRegistrationData, 'date'));
    const registrationCounts = @json(array_column($userRegistrationData, 'count'));
    const activityCounts = @json(array_column($userActivityData, 'count'));
    const challengeCounts = @json(array_column($challengeCompletionData, 'count'));
    
    const growthTrendsData = {
        labels: dates,
        datasets: [
            {
                label: 'Nuevos Usuarios',
                data: registrationCounts,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                pointRadius: 3,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#4e73df',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#4e73df',
                pointHoverBorderColor: '#4e73df',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Usuarios Activos',
                data: activityCounts,
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.05)',
                pointRadius: 3,
                pointBackgroundColor: '#1cc88a',
                pointBorderColor: '#1cc88a',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#1cc88a',
                pointHoverBorderColor: '#1cc88a',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Desafíos Completados',
                data: challengeCounts,
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.05)',
                pointRadius: 3,
                pointBackgroundColor: '#f6c23e',
                pointBorderColor: '#f6c23e',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#f6c23e',
                pointHoverBorderColor: '#f6c23e',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true,
                tension: 0.4
            }
        ]
    };
    
    // Inicializar los gráficos
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de distribución de usuarios
        new Chart(document.getElementById('userDistributionChart'), {
            type: 'doughnut',
            data: userDistributionData,
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
        
        // Gráfico de distribución de actividad
        new Chart(document.getElementById('activityDistributionChart'), {
            type: 'doughnut',
            data: activityDistributionData,
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
        
        // Gráfico de tendencias de crecimiento
        new Chart(document.getElementById('growthTrendsChart'), {
            type: 'line',
            data: growthTrendsData,
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)"
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
    
    // Cambiar período
    document.querySelectorAll('.period-filter').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Actualizar clase activa
            document.querySelectorAll('.period-filter').forEach(el => {
                el.classList.remove('active');
            });
            this.classList.add('active');
            
            // En un caso real, aquí haríamos una petición AJAX para actualizar los datos
            // según el período seleccionado
            const days = this.getAttribute('data-days');
            console.log(`Filtro cambiado a: ${days} días`);
            
            // Simulación de recarga (en un caso real, actualizaríamos los gráficos)
            // window.location.href = `?period=${days}`;
        });
    });
</script>
@endsection 