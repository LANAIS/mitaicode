@extends('layouts.app')

@section('title', 'Mi Inventario - Tienda Mitaí')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('store.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Volver a la tienda
        </a>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <h1 class="h3 mb-0">Mi Inventario</h1>
        </div>
    </div>
    
    <!-- Avatar y estadísticas -->
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Mi Avatar</h2>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-display mb-3">
                        @if($avatar)
                            <img src="{{ $avatar->getAvatarImageUrl() }}" class="img-fluid rounded-circle avatar-img" 
                            style="width: 150px; height: 150px; border: 5px solid #4e73df;" alt="Avatar">
                        @else
                            <div class="default-avatar rounded-circle d-flex align-items-center justify-content-center bg-light"
                            style="width: 150px; height: 150px; border: 5px solid #4e73df; margin: 0 auto;">
                                <i class="fas fa-user-circle text-primary" style="font-size: 100px;"></i>
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <h3 class="h5">{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <p class="mb-1">
                                <span class="badge bg-info">{{ $avatar->current_rank ?? 'Novato' }}</span>
                                @if($avatar && $avatar->current_title)
                                    <span class="badge bg-secondary">{{ $avatar->current_title }}</span>
                                @endif
                            </p>
                            <p class="text-muted small">Nivel {{ $user->studentProfile->level ?? 1 }}</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('store.avatar') }}" class="btn btn-primary">
                        <i class="fas fa-user-edit me-1"></i> Personalizar Avatar
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Items Equipados</h2>
                </div>
                <div class="card-body">
                    @if($equippedItems && $equippedItems->count() > 0)
                        <div class="row g-3">
                            @foreach($equippedItems as $equipItem)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card equipped-item-card">
                                        <div class="position-relative">
                                            @if($equipItem->item->image_path)
                                                <div class="bg-light d-flex align-items-center justify-content-center p-2" style="height: 100px;">
                                                    <div class="bg-white rounded p-1 d-flex align-items-center justify-content-center" style="height: 70px; width: 70px;">
                                                        <img src="{{ asset($equipItem->item->image_path) }}" class="img-fluid" alt="{{ $equipItem->item->name }}" style="max-height: 60px; max-width: 60px; object-fit: contain;">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px;">
                                                    @if($equipItem->item->category == 'avatar')
                                                        <i class="fas fa-user-circle text-primary" style="font-size: 50px;"></i>
                                                    @elseif($equipItem->item->category == 'badge')
                                                        <i class="fas fa-certificate text-success" style="font-size: 50px;"></i>
                                                    @elseif($equipItem->item->category == 'rank')
                                                        <i class="fas fa-crown text-warning" style="font-size: 50px;"></i>
                                                    @elseif($equipItem->item->category == 'skin')
                                                        <i class="fas fa-palette text-info" style="font-size: 50px;"></i>
                                                    @else
                                                        <i class="fas fa-gem text-danger" style="font-size: 50px;"></i>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-success">Equipado</span>
                                            </div>
                                        </div>
                                        <div class="card-body p-2">
                                            <h5 class="card-title h6 mb-0">{{ $equipItem->item->name }}</h5>
                                            <p class="card-text small text-muted">{{ ucfirst($equipItem->item->category) }}</p>
                                            <form action="{{ route('store.unequip', $equipItem->inventory_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100 mt-1">
                                                    <i class="fas fa-times me-1"></i> Desequipar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> No tienes ningún item equipado actualmente.
                            <p class="mt-2 mb-0">Navega tu inventario para equipar tus items y personalizar tu perfil.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Inventario por categorías -->
    <h2 class="h4 mb-3">Mi Colección</h2>
    
    @if($inventory && $inventory->count() > 0)
        <!-- Pestañas para las categorías -->
        <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">
            @foreach($inventory as $category => $items)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                            id="tab-{{ $category }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content-{{ $category }}"
                            type="button"
                            role="tab"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        @if($category == 'avatar')
                            <i class="fas fa-user-circle text-primary me-1"></i>
                        @elseif($category == 'badge')
                            <i class="fas fa-certificate text-success me-1"></i>
                        @elseif($category == 'rank')
                            <i class="fas fa-crown text-warning me-1"></i>
                        @elseif($category == 'skin')
                            <i class="fas fa-palette text-info me-1"></i>
                        @else
                            <i class="fas fa-gem text-danger me-1"></i>
                        @endif
                        {{ ucfirst($category) }}s
                        <span class="badge bg-light text-dark ms-1">{{ count($items) }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
        
        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="inventoryTabsContent">
            @foreach($inventory as $category => $items)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                     id="content-{{ $category }}"
                     role="tabpanel"
                     aria-labelledby="tab-{{ $category }}">
                     
                    <div class="row g-3">
                        @foreach($items as $item)
                            <div class="col-md-6 col-lg-3">
                                <div class="card inventory-item-card h-100">
                                    <div class="position-relative">
                                        @if($item->item->image_path)
                                            <div class="bg-light d-flex align-items-center justify-content-center p-3" style="height: 160px;">
                                                <div class="bg-white rounded p-2 d-flex align-items-center justify-content-center" style="height: 100px; width: 100px;">
                                                    <img src="{{ asset($item->item->image_path) }}" class="img-fluid" alt="{{ $item->item->name }}" style="max-height: 80px; max-width: 80px; object-fit: contain;">
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
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
                                        
                                        @if($item->is_equipped)
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-success">Equipado</span>
                                            </div>
                                        @endif
                                        
                                        @if($item->expires_at)
                                            <div class="position-absolute top-0 start-0 m-2">
                                                <span class="badge bg-warning">
                                                    @if($item->hasExpired())
                                                        Expirado
                                                    @else
                                                        Expira: {{ $item->expires_at->diffForHumans() }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $item->item->name }}</h5>
                                        <p class="card-text small text-muted">
                                            {{ Str::limit($item->item->description, 60) }}
                                        </p>
                                        <p class="card-text small">
                                            <i class="fas fa-calendar-alt me-1 text-muted"></i> Adquirido: {{ $item->acquired_at->format('d/m/Y') }}
                                        </p>
                                        
                                        <div class="d-grid gap-2">
                                            @if($item->is_equipped)
                                                <form action="{{ route('store.unequip', $item->inventory_id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                        <i class="fas fa-times me-1"></i> Desequipar
                                                    </button>
                                                </form>
                                            @elseif(!$item->hasExpired())
                                                <form action="{{ route('store.equip', $item->inventory_id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                                        <i class="fas fa-check me-1"></i> Equipar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-1"></i> Todavía no tienes ningún item en tu inventario.
            <p class="mt-2 mb-0">Visita la <a href="{{ route('store.index') }}" class="alert-link">Tienda</a> para adquirir items con tus puntos XP.</p>
        </div>
    @endif
</div>

<style>
    .inventory-item-card {
        transition: all 0.3s ease;
    }
    
    .inventory-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .equipped-item-card {
        transition: all 0.3s ease;
        border: 2px solid #28a745;
    }
    
    .equipped-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .avatar-img {
        transition: all 0.3s ease;
        object-fit: cover;
    }
    
    .avatar-img:hover {
        transform: scale(1.05);
    }
</style>
@endsection 