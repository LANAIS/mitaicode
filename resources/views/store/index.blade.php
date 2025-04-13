@extends('layouts.app')

@section('title', 'Tienda Mitaí')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tienda Mitaí</h1>
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
            <h2 class="h5 mb-3">¿Qué es la Tienda Mitaí?</h2>
            <p>Aquí puedes gastar tus puntos XP obtenidos al completar desafíos y misiones para desbloquear:
            </p>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <i class="fas fa-user-astronaut text-primary" style="font-size: 2rem;"></i>
                        <h3 class="h6 mt-2">Avatares Personalizados</h3>
                        <p class="small text-muted">Personaliza tu apariencia en la plataforma</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <i class="fas fa-medal text-warning" style="font-size: 2rem;"></i>
                        <h3 class="h6 mt-2">Rangos y Títulos</h3>
                        <p class="small text-muted">Muestra tu experiencia y estatus</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <i class="fas fa-palette text-success" style="font-size: 2rem;"></i>
                        <h3 class="h6 mt-2">Temas y Decoraciones</h3>
                        <p class="small text-muted">Personaliza la apariencia de tu perfil</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Categorías -->
    <h2 class="h4 mb-3">Categorías</h2>
    <div class="row g-3 mb-5">
        @foreach($categories as $slug => $name)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('store.category', $slug) }}" class="text-decoration-none">
                <div class="card h-100 store-category-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="category-icon me-3">
                            @if($slug == 'avatar')
                            <i class="fas fa-user-astronaut text-primary" style="font-size: 2rem;"></i>
                            @elseif($slug == 'badge')
                            <i class="fas fa-certificate text-success" style="font-size: 2rem;"></i>
                            @elseif($slug == 'rank')
                            <i class="fas fa-crown text-warning" style="font-size: 2rem;"></i>
                            @elseif($slug == 'skin')
                            <i class="fas fa-palette text-info" style="font-size: 2rem;"></i>
                            @else
                            <i class="fas fa-gem text-danger" style="font-size: 2rem;"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="h5 mb-1">{{ $name }}</h3>
                            <p class="text-muted mb-0 small">Explora todos los {{ strtolower($name) }} disponibles</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Productos Destacados -->
    <h2 class="h4 mb-3">Destacados</h2>
    <div class="row g-3 mb-4">
        @forelse($featuredItems as $item)
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 store-item-card">
                <div class="position-relative">
                    @if($item->image_path)
                    <div class="card-img-top bg-light d-flex flex-column align-items-center justify-content-center p-3" style="height: 160px;">
                        <div class="bg-white rounded p-2 d-flex align-items-center justify-content-center" style="height: 100px; width: 100px;">
                            <img src="{{ asset($item->image_path) }}" class="img-fluid" alt="{{ $item->name }}" style="max-height: 80px; max-width: 80px; object-fit: contain;">
                        </div>
                    </div>
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                        @if($item->category == 'avatar')
                        <i class="fas fa-user-circle text-primary" style="font-size: 60px;"></i>
                        @elseif($item->category == 'badge')
                        <i class="fas fa-certificate text-success" style="font-size: 60px;"></i>
                        @elseif($item->category == 'rank')
                        <i class="fas fa-crown text-warning" style="font-size: 60px;"></i>
                        @elseif($item->category == 'skin')
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
                No hay productos destacados en este momento.
            </div>
        </div>
        @endforelse
    </div>

    <!-- Nuevos Accesorios -->
    <h2 class="h4 mb-3">Nuevos Accesorios para Avatar</h2>
    <div class="row g-3 mb-5">
        @forelse($avatarAccessories as $accessory)
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 store-item-card">
                <div class="card-img-top bg-light d-flex flex-column align-items-center justify-content-center p-3" style="height: 160px;">
                    @if($accessory->image_path)
                    <div class="bg-white rounded p-2 d-flex align-items-center justify-content-center" style="height: 100px; width: 100px;">
                        <img src="{{ asset($accessory->image_path) }}" alt="{{ $accessory->name }}" style="height: 80px; width: 80px; object-fit: contain;">
                    </div>
                    @else
                    <div class="d-flex align-items-center justify-content-center" style="height: 80px; width: 80px;">
                        <i class="fas fa-user-circle text-primary" style="font-size: 60px;"></i>
                    </div>
                    @endif
                    <h5 class="mt-2">{{ $accessory->name }}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text small text-muted">{{ Str::limit($accessory->description, 60) }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="badge bg-primary p-2">
                            <i class="fas fa-star me-1"></i> {{ $accessory->price }} XP
                        </span>
                        
                        @if(isset($userInventory[$accessory->item_id]))
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i> Adquirido
                            </span>
                        @else
                            <a href="{{ route('store.show', $accessory->slug) }}" class="btn btn-sm btn-outline-primary">
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
                No hay accesorios disponibles en este momento.
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    .store-category-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,.125);
    }
    
    .store-category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-color: #4e73df;
    }
    
    .store-item-card {
        transition: all 0.3s ease;
    }
    
    .store-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endsection 