@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
<style>
    .stat-card {
        transition: all 0.3s;
        background-color: #fff;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .table {
        color: #333;
    }
    
    .table th {
        color: #495057;
        font-weight: 600;
    }
    
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }
    
    .card-header {
        color: #333;
    }
    
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    
    .text-primary {
        color: #4e73df !important;
    }
    
    .text-success {
        color: #1cc88a !important;
    }
    
    .text-info {
        color: #36b9cc !important;
    }
    
    .text-warning {
        color: #f6c23e !important;
    }
    
    .text-gray-800 {
        color: #3a3b45 !important;
    }
    
    .text-gray-300 {
        color: #dddfeb !important;
    }
    
    .font-weight-bold {
        font-weight: 700 !important;
    }
    
    .badge {
        color: white;
    }
    
    .badge.bg-gradient-secondary {
        background-color: #858796;
    }
    
    .badge.bg-gradient-success {
        background-color: #1cc88a;
    }
    
    .badge.bg-gradient-info {
        background-color: #36b9cc;
    }
    
    .badge.bg-gradient-warning {
        background-color: #f6c23e;
        color: #333;
    }
    
    .badge.bg-gradient-danger {
        background-color: #e74a3b;
    }
    
    .email-preview {
        padding: 15px;
        border: 1px solid #e3e6f0;
        border-radius: 5px;
        background-color: #f8f9fc;
        color: #333;
    }
    
    .text-muted {
        color: #666 !important;
    }
    
    .text-secondary {
        color: #858796 !important;
    }
    
    .text-dark {
        color: #343a40 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3 pt-2">Estadísticas de Notificación: {{ $notification->name }}</h6>
                        <div class="pe-3">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm bg-white text-dark">
                                <i class="fas fa-arrow-left text-sm"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-dark">Detalles de la Notificación</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>Tipo:</th>
                                            <td><span class="badge bg-gradient-{{ $notification->type }}">{{ $notification->type }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Asunto:</th>
                                            <td class="text-dark">{{ $notification->subject }}</td>
                                        </tr>
                                        <tr>
                                            <th>Evento Disparador:</th>
                                            <td class="text-dark">{{ $notification->trigger_event }}</td>
                                        </tr>
                                        <tr>
                                            <th>Estado:</th>
                                            <td>
                                                <span class="badge bg-gradient-{{ $notification->is_active ? 'success' : 'secondary' }}">
                                                    {{ $notification->is_active ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Hora de envío:</th>
                                            <td class="text-dark">{{ date('H:i', strtotime($notification->send_time)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Último envío:</th>
                                            <td class="text-dark">{{ $notification->last_sent_at ? date('d/m/Y H:i', strtotime($notification->last_sent_at)) : 'Nunca' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header text-dark">
                                    Vista previa del mensaje
                                </div>
                                <div class="card-body bg-light">
                                    <div class="email-preview">
                                        {!! $notification->content !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Envíos Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats->total_sent ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Enviados Exitosamente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats->successful_sent ?? 0) }}</div>
                            @if(isset($stats->total_sent) && $stats->total_sent > 0)
                            <div class="text-xs mt-2">
                                <span class="text-success">{{ round(($stats->successful_sent / $stats->total_sent) * 100, 1) }}%</span> tasa de éxito
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Abiertos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats->total_opened ?? 0) }}</div>
                            @if(isset($stats->successful_sent) && $stats->successful_sent > 0)
                            <div class="text-xs mt-2">
                                <span class="text-info">{{ round(($stats->total_opened / $stats->successful_sent) * 100, 1) }}%</span> ratio de apertura
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Clics</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats->total_clicked ?? 0) }}</div>
                            @if(isset($stats->total_opened) && $stats->total_opened > 0)
                            <div class="text-xs mt-2">
                                <span class="text-warning">{{ round(($stats->total_clicked / $stats->total_opened) * 100, 1) }}%</span> ratio de clics
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráfico de tendencia -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tendencia de Envíos (14 días)</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="notificationTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Registros de envíos -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Registros de Envíos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Usuario</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Abierto</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Clic</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm text-dark">Usuario #{{ $log->user_id }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold text-dark mb-0">{{ $log->email }}</p>
                                    </td>
                                    <td>
                                        @if($log->sent)
                                            <span class="badge badge-sm bg-gradient-success">Enviado</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-danger">Fallido</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->opened)
                                            <span class="badge badge-sm bg-gradient-info">Sí</span>
                                        @else
                                            <span class="text-xs text-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->clicked)
                                            <span class="badge badge-sm bg-gradient-warning">Sí</span>
                                        @else
                                            <span class="text-xs text-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-xs text-secondary mb-0">{{ date('d/m/Y H:i', strtotime($log->created_at)) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted">No hay registros de envío para esta notificación</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($logs->count() > 0)
                    <div class="d-flex justify-content-center mt-3">
                        {{ $logs->links() }}
                    </div>
                    @endif
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
        // Datos para la gráfica de tendencia
        const ctx = document.getElementById('notificationTrendChart').getContext('2d');
        
        const trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($dailyStats->pluck('date')),
                datasets: [
                    {
                        label: 'Enviados',
                        data: @json($dailyStats->pluck('total_sent')),
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        fill: true,
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Abiertos',
                        data: @json($dailyStats->pluck('total_opened')),
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                        pointBorderColor: 'rgba(28, 200, 138, 1)',
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: 'rgba(28, 200, 138, 1)',
                        pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        fill: false,
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Clics',
                        data: @json($dailyStats->pluck('total_clicked')),
                        backgroundColor: 'rgba(246, 194, 62, 0.05)',
                        borderColor: 'rgba(246, 194, 62, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(246, 194, 62, 1)',
                        pointBorderColor: 'rgba(246, 194, 62, 1)',
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: 'rgba(246, 194, 62, 1)',
                        pointHoverBorderColor: 'rgba(246, 194, 62, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        fill: false,
                        borderWidth: 2,
                        tension: 0.3
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                    },
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
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
                }
            }
        });
    });
</script>
@endsection 