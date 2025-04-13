@extends('layouts.app')

@section('title', $categoryName . ' - Tienda Mitaí')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('store.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Volver a la tienda
            </a>
            <h1 class="h3 mb-0">{{ $categoryName }}</h1>
        </div>
        <div class="d-flex align-items-center">
            <div class="me-4">
                <span class="badge bg-primary p-2 d-flex align-items-center">
                    <i class="fas fa-star me-1"></i>
                    <span class="h5 mb-0">{{ $xpPoints }} XP</span>
                </span>
            </div>
            <a href="{{ route('store.inventory') }}" class="btn btn-outline-primary">
                <i class="fas fa-box me-1"></i> Mi Inventario
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @if($category == 'avatar')
                <p>Los avatares te permiten personalizar tu apariencia en la plataforma. ¡Hazte único!</p>
            @elseif($category == 'badge')
                <p>Las insignias son símbolos de tus logros y hazañas. ¡Colecciónalas todas!</p>
            @elseif($category == 'rank')
                <p>Los rangos muestran tu nivel de experiencia y dedicación. ¡Sube de rango y demuestra tu valía!</p>
            @elseif($category == 'skin')
                <p>Los temas cambian la apariencia de tu perfil y la interfaz. ¡Personaliza tu experiencia!</p>
            @else
                <p>Elementos especiales con poderes y beneficios únicos. ¡Descubre sus efectos!</p>
            @endif
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label for="sortItems" class="form-label mb-0">Ordenar por:</label>
                    <select id="sortItems" class="form-select">
                        <option value="price_asc">Precio: menor a mayor</option>
                        <option value="price_desc">Precio: mayor a menor</option>
                        <option value="level_asc">Nivel: menor a mayor</option>
                        <option value="level_desc">Nivel: mayor a menor</option>
                        <option value="name_asc">Nombre: A-Z</option>
                        <option value="name_desc">Nombre: Z-A</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <div class="d-flex justify-content-md-end mt-3 mt-md-0">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="showAll" checked>
                            <label class="form-check-label" for="showAll">Todos</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="showAvailable">
                            <label class="form-check-label" for="showAvailable">Disponibles</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="showPurchased">
                            <label class="form-check-label" for="showPurchased">Adquiridos</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Items -->
    <div class="row g-3 mb-4" id="itemsContainer">
        @forelse($items as $item)
        <div class="col-md-6 col-lg-3 item-card" 
             data-price="{{ $item->price }}" 
             data-level="{{ $item->level_required }}" 
             data-name="{{ $item->name }}"
             data-purchased="{{ isset($userInventory[$item->item_id]) ? 'true' : 'false' }}">
            <div class="card h-100 store-item-card">
                <div class="position-relative">
                    @if($item->image_path)
                    <div class="bg-light d-flex align-items-center justify-content-center p-3" style="height: 160px;">
                        <div class="bg-white rounded p-2 d-flex align-items-center justify-content-center" style="height: 100px; width: 100px;">
                            <img src="{{ asset($item->image_path) }}" class="img-fluid" alt="{{ $item->name }}" style="max-height: 80px; max-width: 80px; object-fit: contain;">
                        </div>
                    </div>
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                        @if($category == 'avatar')
                        <i class="fas fa-user-circle text-primary" style="font-size: 60px;"></i>
                        @elseif($category == 'badge')
                        <i class="fas fa-certificate text-success" style="font-size: 60px;"></i>
                        @elseif($category == 'rank')
                        <i class="fas fa-crown text-warning" style="font-size: 60px;"></i>
                        @elseif($category == 'skin')
                        <i class="fas fa-palette text-info" style="font-size: 60px;"></i>
                        @else
                        <i class="fas fa-gem text-danger" style="font-size: 60px;"></i>
                        @endif
                    </div>
                    @endif
                    
                    @if($item->level_required > 1)
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-warning">Nivel {{ $item->level_required }}+</span>
                    </div>
                    @endif
                    
                    @if($item->is_limited)
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-danger">Limitado</span>
                    </div>
                    @endif
                </div>
                
                <div class="card-body">
                    <h5 class="card-title">{{ $item->name }}</h5>
                    <p class="card-text small text-muted">{{ Str::limit($item->description, 60) }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="badge bg-primary p-2">
                            <i class="fas fa-star me-1"></i> {{ $item->price }} XP
                        </span>
                        
                        @if(isset($userInventory[$item->item_id]))
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i> Adquirido
                            </span>
                        @else
                            <a href="{{ route('store.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">
                                Ver detalles
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                No hay items disponibles en esta categoría.
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    .store-item-card {
        transition: all 0.3s ease;
    }
    
    .store-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sortItems');
    const showAllCheckbox = document.getElementById('showAll');
    const showAvailableCheckbox = document.getElementById('showAvailable');
    const showPurchasedCheckbox = document.getElementById('showPurchased');
    const itemsContainer = document.getElementById('itemsContainer');
    const itemCards = document.querySelectorAll('.item-card');
    
    // Función para ordenar los elementos
    function sortItems() {
        const items = Array.from(itemCards);
        const sortValue = sortSelect.value;
        
        items.sort(function(a, b) {
            switch(sortValue) {
                case 'price_asc':
                    return parseInt(a.dataset.price) - parseInt(b.dataset.price);
                case 'price_desc':
                    return parseInt(b.dataset.price) - parseInt(a.dataset.price);
                case 'level_asc':
                    return parseInt(a.dataset.level) - parseInt(b.dataset.level);
                case 'level_desc':
                    return parseInt(b.dataset.level) - parseInt(a.dataset.level);
                case 'name_asc':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'name_desc':
                    return b.dataset.name.localeCompare(a.dataset.name);
                default:
                    return 0;
            }
        });
        
        // Limpiamos el contenedor
        itemsContainer.innerHTML = '';
        
        // Agregamos los elementos ordenados
        items.forEach(item => {
            itemsContainer.appendChild(item);
        });
        
        // Aplicamos los filtros
        filterItems();
    }
    
    // Función para filtrar los elementos
    function filterItems() {
        const showAll = showAllCheckbox.checked;
        const showAvailable = showAvailableCheckbox.checked;
        const showPurchased = showPurchasedCheckbox.checked;
        
        itemCards.forEach(item => {
            const isPurchased = item.dataset.purchased === 'true';
            
            if (showAll || (showAvailable && !isPurchased) || (showPurchased && isPurchased)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // Eventos para ordenar y filtrar
    sortSelect.addEventListener('change', sortItems);
    showAllCheckbox.addEventListener('change', function() {
        // Si se marca "Todos", desmarcamos los otros
        if (this.checked) {
            showAvailableCheckbox.checked = false;
            showPurchasedCheckbox.checked = false;
        } else if (!showAvailableCheckbox.checked && !showPurchasedCheckbox.checked) {
            // Si se desmarca y ningún otro está marcado, lo volvemos a marcar
            this.checked = true;
        }
        filterItems();
    });
    
    showAvailableCheckbox.addEventListener('change', function() {
        if (this.checked) {
            showAllCheckbox.checked = false;
        } else if (!showPurchasedCheckbox.checked && !showAllCheckbox.checked) {
            showAllCheckbox.checked = true;
        }
        filterItems();
    });
    
    showPurchasedCheckbox.addEventListener('change', function() {
        if (this.checked) {
            showAllCheckbox.checked = false;
        } else if (!showAvailableCheckbox.checked && !showAllCheckbox.checked) {
            showAllCheckbox.checked = true;
        }
        filterItems();
    });
});
</script>
@endsection 