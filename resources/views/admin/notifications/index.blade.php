@extends('layouts.admin')

@section('styles')
<style>
    .table-responsive {
        background-color: #fff;
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
    }
    
    .badge-primary {
        background-color: #4e73df;
        color: white;
    }
    
    .badge-success {
        background-color: #1cc88a;
        color: white;
    }
    
    .badge-warning {
        background-color: #f6c23e;
        color: #333;
    }
    
    .badge-danger {
        background-color: #e74a3b;
        color: white;
    }
    
    .badge-info {
        background-color: #36b9cc;
        color: white;
    }
    
    .badge-secondary {
        background-color: #858796;
        color: white;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .text-dark {
        color: #343a40 !important;
    }
    
    .text-muted {
        color: #666 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between">
                        <h6 class="text-white text-capitalize ps-3 pt-2">Notificaciones por Email</h6>
                        <div class="pe-3">
                            <a href="{{ route('admin.notifications.create') }}" class="btn btn-sm bg-white text-dark">
                                <i class="fas fa-plus text-sm"></i> Nueva Notificación
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipo</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Último Envío</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estadísticas</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notification)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm text-dark">{{ $notification->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $notification->subject }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-gradient-info">{{ $notification->type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $notification->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                            {{ $notification->is_active ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $notification->last_sent_at ? date('d/m/Y H:i', strtotime($notification->last_sent_at)) : 'Nunca' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.notifications.stats', $notification->id) }}" class="btn btn-sm bg-gradient-info">
                                            <i class="fas fa-chart-line text-xs"></i> Ver Estadísticas
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('admin.notifications.test', $notification->id) }}" class="btn btn-sm bg-gradient-warning me-1" data-toggle="tooltip" title="Enviar prueba">
                                            <i class="fas fa-paper-plane text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="btn btn-sm bg-gradient-info me-1" data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-gradient-danger" data-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar esta notificación?')">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted">No hay notificaciones configuradas</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($notifications->count() > 0)
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $notifications->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 