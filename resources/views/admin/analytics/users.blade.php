@extends('layouts.admin')

@section('title', 'Análisis de Usuarios')

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
    
    .user-stat {
        text-align: center;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.08);
        transition: all 0.3s;
    }
    
    .user-stat:hover {
        transform: translateY(-5px);
    }
    
    .user-stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: #4e73df;
    }
    
    .progress-small {
        height: 8px;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Análisis de Usuarios</h1>
        <div>
            <a href="{{ route('admin.analytics.export', ['data_type' => 'users']) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50 mr-1"></i> Exportar Datos
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Métricas principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 metric-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Usuarios Activos (Hoy)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($userActivity['active_today']) }}</div>
                            <div class="text-xs mt-2">
                                <span class="text-success"><i class="fas fa-check-circle mr-1"></i>{{ round(($userActivity['active_today'] / array_sum($usersByRole)) * 100, 1) }}%</span> del total
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 metric-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tasa de Retención</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $retentionRate }}%</div>
                            <div class="text-xs mt-2">
                                <span class="text-info"><i class="fas fa-info-circle mr-1"></i>Últimos 30 días</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 metric-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tiempo Promedio de Sesión</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgSessionTime }} min</div>
                            <div class="text-xs mt-2">
                                <span class="text-muted"><i class="fas fa-clock mr-1"></i>Por usuario activo</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 metric-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Usuarios Inactivos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($userActivity['inactive_30days']) }}</div>
                            <div class="text-xs mt-2">
                                <span class="text-danger"><i class="fas fa-exclamation-circle mr-1"></i>Inactivos >30 días</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-slash fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución de Usuarios y Actividad -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución por Rol</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="userRoleChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="user-stat bg-light">
                                    <i class="fas fa-user-graduate user-stat-icon text-primary"></i>
                                    <h5>{{ number_format($usersByRole['students']) }}</h5>
                                    <p class="mb-0 text-muted">Estudiantes</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="user-stat bg-light">
                                    <i class="fas fa-chalkboard-teacher user-stat-icon text-success"></i>
                                    <h5>{{ number_format($usersByRole['teachers']) }}</h5>
                                    <p class="mb-0 text-muted">Profesores</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="user-stat bg-light">
                                    <i class="fas fa-user-shield user-stat-icon text-info"></i>
                                    <h5>{{ number_format($usersByRole['admins']) }}</h5>
                                    <p class="mb-0 text-muted">Administradores</p>
                                </div>
                            </div>
                            @if(isset($usersByRole['otros']) && $usersByRole['otros'] > 0)
                            <div class="col-md-4 mt-3 mx-auto">
                                <div class="user-stat bg-light">
                                    <i class="fas fa-user-tag user-stat-icon text-warning"></i>
                                    <h5>{{ number_format($usersByRole['otros']) }}</h5>
                                    <p class="mb-0 text-muted">Sin rol o roles no definidos</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Actividad de Usuarios</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="userActivityChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold">Activos hoy</h6>
                                <div class="progress progress-small mb-2">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                        style="width: {{ ($userActivity['active_today'] / array_sum($usersByRole)) * 100 }}%" 
                                        aria-valuenow="{{ ($userActivity['active_today'] / array_sum($usersByRole)) * 100 }}" 
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">{{ number_format($userActivity['active_today']) }} usuarios</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold">Activos (7 días)</h6>
                                <div class="progress progress-small mb-2">
                                    <div class="progress-bar bg-info" role="progressbar" 
                                        style="width: {{ ($userActivity['active_7days'] / array_sum($usersByRole)) * 100 }}%" 
                                        aria-valuenow="{{ ($userActivity['active_7days'] / array_sum($usersByRole)) * 100 }}" 
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">{{ number_format($userActivity['active_7days']) }} usuarios</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold">Activos (30 días)</h6>
                                <div class="progress progress-small mb-2">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                        style="width: {{ ($userActivity['active_30days'] / array_sum($usersByRole)) * 100 }}%" 
                                        aria-valuenow="{{ ($userActivity['active_30days'] / array_sum($usersByRole)) * 100 }}" 
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">{{ number_format($userActivity['active_30days']) }} usuarios</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="font-weight-bold">Inactivos (>30 días)</h6>
                                <div class="progress progress-small mb-2">
                                    <div class="progress-bar bg-danger" role="progressbar" 
                                        style="width: {{ ($userActivity['inactive_30days'] / array_sum($usersByRole)) * 100 }}%" 
                                        aria-valuenow="{{ ($userActivity['inactive_30days'] / array_sum($usersByRole)) * 100 }}" 
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">{{ number_format($userActivity['inactive_30days']) }} usuarios</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tendencias de Crecimiento y Niveles -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tendencia de Registro de Usuarios</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribución por Nivel</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="userLevelChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nivel</th>
                                    <th>Estudiantes</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usersByLevel as $level => $count)
                                <tr>
                                    <td>{{ $level }}</td>
                                    <td>{{ number_format($count) }}</td>
                                    <td>{{ round(($count / $usersByRole['students']) * 100, 1) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas informativas -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card bg-primary text-white mb-4">
                <div class="card-header border-0">
                    <h5 class="m-0 font-weight-bold">Consejos de engagement</h5>
                </div>
                <div class="card-body">
                    <p>Revisa los {{ number_format($userActivity['inactive_30days']) }} usuarios inactivos y considera:</p>
                    <ul>
                        <li>Enviar correos de re-activación</li>
                        <li>Crear desafíos con recompensas especiales</li>
                        <li>Implementar notificaciones push</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card bg-success text-white mb-4">
                <div class="card-header border-0">
                    <h5 class="m-0 font-weight-bold">Estrategias de crecimiento</h5>
                </div>
                <div class="card-body">
                    <p>Para aumentar la base de usuarios:</p>
                    <ul>
                        <li>Programa de referidos para estudiantes</li>
                        <li>Eventos virtuales para captar nuevos usuarios</li>
                        <li>Colaboraciones con instituciones educativas</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card bg-info text-white mb-4">
                <div class="card-header border-0">
                    <h5 class="m-0 font-weight-bold">Próximos pasos</h5>
                </div>
                <div class="card-body">
                    <p>Acciones recomendadas basadas en los datos:</p>
                    <ul>
                        <li>Implementar encuestas de satisfacción</li>
                        <li>Añadir funcionalidades solicitadas por usuarios activos</li>
                        <li>Simplificar proceso de onboarding</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de depuración -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Información para depuración</h6>
                </div>
                <div class="card-body">
                    <h5>Resumen de usuarios</h5>
                    <p>Total de usuarios en el sistema: <strong>{{ $totalUsers }}</strong></p>
                    
                    <div class="mt-4">
                        <h5>Usuarios recientes (últimos 5 registros)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Fecha de registro</th>
                                        <th>Última conexión</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                    <tr>
                                        <td>{{ $user->user_id }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role ?: 'Sin rol' }}</td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
        // Gráfico de distribución por rol
        const userRoleChart = new Chart(document.getElementById('userRoleChart'), {
            type: 'pie',
            data: {
                labels: [
                    'Estudiantes', 
                    'Profesores', 
                    'Administradores'
                    @if(isset($usersByRole['otros']) && $usersByRole['otros'] > 0)
                    , 'Sin rol o roles no definidos'
                    @endif
                ],
                datasets: [{
                    data: [
                        {{ $usersByRole['students'] }},
                        {{ $usersByRole['teachers'] }},
                        {{ $usersByRole['admins'] }}
                        @if(isset($usersByRole['otros']) && $usersByRole['otros'] > 0)
                        , {{ $usersByRole['otros'] }}
                        @endif
                    ],
                    backgroundColor: [
                        '#4e73df', 
                        '#1cc88a', 
                        '#36b9cc'
                        @if(isset($usersByRole['otros']) && $usersByRole['otros'] > 0)
                        , '#f6c23e'
                        @endif
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9', 
                        '#17a673', 
                        '#258391'
                        @if(isset($usersByRole['otros']) && $usersByRole['otros'] > 0)
                        , '#dda20a'
                        @endif
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
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
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '50%'
            }
        });
        
        // Datos para el gráfico de actividad de usuarios
        const userActivityChart = new Chart(document.getElementById('userActivityChart'), {
            type: 'bar',
            data: {
                labels: ['Activos hoy', 'Activos 7 días', 'Activos 30 días', 'Inactivos >30 días'],
                datasets: [{
                    label: 'Usuarios',
                    data: [
                        {{ $userActivity['active_today'] }},
                        {{ $userActivity['active_7days'] }},
                        {{ $userActivity['active_30days'] }},
                        {{ $userActivity['inactive_30days'] }}
                    ],
                    backgroundColor: ['#1cc88a', '#36b9cc', '#4e73df', '#e74a3b'],
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
        
        // Datos para el gráfico de crecimiento mensual
        const userGrowthChart = new Chart(document.getElementById('userGrowthChart'), {
            type: 'line',
            data: {
                labels: @json(array_column($usersByMonth, 'month')),
                datasets: [{
                    label: 'Nuevos Usuarios',
                    data: @json(array_column($usersByMonth, 'count')),
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
        
        // Datos para el gráfico de niveles de usuarios
        const userLevelChart = new Chart(document.getElementById('userLevelChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(@json($usersByLevel)),
                datasets: [{
                    label: 'Estudiantes',
                    data: Object.values(@json($usersByLevel)),
                    backgroundColor: '#4e73df',
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
    });
</script>
@endsection 