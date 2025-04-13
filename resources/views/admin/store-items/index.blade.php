@extends('layouts.admin')

@section('title', 'Gestión de Items de Tienda - Panel de Administración')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Items de Tienda</h1>
        <a href="{{ route('admin.store-items.create') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Crear Nuevo Item
        </a>
    </div>

    <!-- Tarjeta principal -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Items</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-filter fa-sm fa-fw text-gray-400"></i> Filtrar
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="filterDropdown">
                    <div class="dropdown-header">Categorías:</div>
                    <a class="dropdown-item filter-item" href="#" data-filter="all">Todos</a>
                    <a class="dropdown-item filter-item" href="#" data-filter="avatar">Avatares</a>
                    <a class="dropdown-item filter-item" href="#" data-filter="badge">Insignias</a>
                    <a class="dropdown-item filter-item" href="#" data-filter="rank">Rangos</a>
                    <a class="dropdown-item filter-item" href="#" data-filter="skin">Temas</a>
                    <a class="dropdown-item filter-item" href="#" data-filter="special">Especiales</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Nivel Req.</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr data-category="{{ $item->category }}">
                                <td class="text-center">
                                    @if($item->image_path)
                                        <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" class="img-thumbnail" style="max-height: 50px;">
                                    @else
                                        <span class="text-muted"><i class="fas fa-image fa-2x"></i></span>
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <span class="badge badge-{{ getCategoryBadgeClass($item->category) }}">
                                        {{ getCategoryName($item->category) }}
                                    </span>
                                </td>
                                <td>{{ $item->type }}</td>
                                <td>{{ $item->price }} <i class="fas fa-gem text-primary fa-sm"></i></td>
                                <td>{{ $item->level_required }}</td>
                                <td>
                                    @if($item->is_limited)
                                        {{ $item->stock ?? 0 }}
                                    @else
                                        <span class="text-muted">Ilimitado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle btn btn-sm btn-light" href="#" role="button" id="dropdownMenuLink{{ $item->item_id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink{{ $item->item_id }}">
                                            <a class="dropdown-item" href="{{ route('admin.store-items.edit', $item) }}">
                                                <i class="fas fa-pencil-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Editar
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.store-items.destroy', $item) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash fa-sm fa-fw mr-2 text-gray-400"></i>
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializar DataTable
        const table = $('#itemsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            order: [[1, 'asc']], // Ordenar por nombre
            columnDefs: [
                { orderable: false, targets: [0, 8] } // Columnas no ordenables
            ]
        });
        
        // Filtrar por categoría
        $('.filter-item').on('click', function(e) {
            e.preventDefault();
            const filter = $(this).data('filter');
            
            if (filter === 'all') {
                table.columns(2).search('').draw();
            } else {
                table.columns(2).search(filter).draw();
            }
        });
        
        // Confirmar eliminación
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            
            if (confirm('¿Estás seguro de que deseas eliminar este item? Esta acción no se puede deshacer.')) {
                this.submit();
            }
        });
    });
    
    // Funciones auxiliares para el template
    function getCategoryName(category) {
        const categories = {
            'avatar': 'Avatar',
            'badge': 'Insignia',
            'rank': 'Rango',
            'skin': 'Tema',
            'special': 'Especial'
        };
        
        return categories[category] || category;
    }
    
    function getCategoryBadgeClass(category) {
        const classes = {
            'avatar': 'info',
            'badge': 'success',
            'rank': 'warning',
            'skin': 'primary',
            'special': 'danger'
        };
        
        return classes[category] || 'secondary';
    }
</script>
@endsection 