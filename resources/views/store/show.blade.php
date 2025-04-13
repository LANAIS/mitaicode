@extends('layouts.app')

@section('title', $item->name . ' - Tienda Mitaí')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('store.category', $item->category) }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Volver a {{ ucfirst($item->category) }}s
        </a>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <h1 class="h3 mb-0">{{ $item->name }}</h1>
            <div>
                <span class="badge bg-primary p-2 d-flex align-items-center">
                    <i class="fas fa-star me-1"></i>
                    <span class="h5 mb-0">{{ $xpPoints }} XP</span>
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="card shadow-sm">
                <div class="position-relative">
                    @if($item->image_path)
                    <div class="bg-light d-flex align-items-center justify-content-center p-3" style="height: 300px;">
                        <div class="bg-white rounded p-3 d-flex align-items-center justify-content-center" style="height: 200px; width: 200px;">
                            <img src="{{ asset($item->image_path) }}" class="img-fluid" alt="{{ $item->name }}" style="max-height: 160px; max-width: 160px; object-fit: contain;">
                        </div>
                    </div>
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        @if($item->category == 'avatar')
                        <i class="fas fa-user-circle text-primary" style="font-size: 120px;"></i>
                        @elseif($item->category == 'badge')
                        <i class="fas fa-certificate text-success" style="font-size: 120px;"></i>
                        @elseif($item->category == 'rank')
                        <i class="fas fa-crown text-warning" style="font-size: 120px;"></i>
                        @elseif($item->category == 'skin')
                        <i class="fas fa-palette text-info" style="font-size: 120px;"></i>
                        @else
                        <i class="fas fa-gem text-danger" style="font-size: 120px;"></i>
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
                        <span class="badge bg-danger">Limitado ({{ $item->stock ?: 'Agotado' }})</span>
                    </div>
                    @endif
                </div>
                
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <span class="badge bg-primary p-2 fs-5">
                            <i class="fas fa-star me-1"></i> {{ $item->price }} XP
                        </span>
                    </div>
                    
                    @if($hasItem)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-1"></i> Ya has adquirido este item
                        </div>
                        <a href="{{ route('store.inventory') }}" class="btn btn-primary">
                            <i class="fas fa-box me-1"></i> Ver en mi inventario
                        </a>
                    @elseif(!$canPurchase)
                        @if($xpPoints < $item->price)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> No tienes suficientes puntos XP
                            </div>
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-lock me-1"></i> Te faltan {{ $item->price - $xpPoints }} XP
                            </button>
                        @elseif($user->studentProfile && $user->studentProfile->level < $item->level_required)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> Nivel insuficiente
                            </div>
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-lock me-1"></i> Necesitas nivel {{ $item->level_required }}
                            </button>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i> No disponible
                            </div>
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-lock me-1"></i> No disponible
                            </button>
                        @endif
                    @else
                        <form action="{{ route('store.purchase', $item->item_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-cart me-1"></i> Comprar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Descripción</h2>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $item->description }}</p>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Detalles</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-tag me-2 text-muted"></i> Categoría:</span>
                                    <span class="fw-bold">{{ ucfirst($item->category) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-stream me-2 text-muted"></i> Tipo:</span>
                                    <span class="fw-bold">{{ ucfirst($item->type) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-chart-line me-2 text-muted"></i> Nivel requerido:</span>
                                    <span class="fw-bold">{{ $item->level_required }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-star me-2 text-muted"></i> Precio:</span>
                                    <span class="fw-bold">{{ $item->price }} XP</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-hourglass-half me-2 text-muted"></i> Disponibilidad:</span>
                                    <span class="fw-bold">
                                        @if($item->is_limited)
                                            Limitado ({{ $item->stock ?: 'Agotado' }})
                                        @else
                                            Permanente
                                        @endif
                                    </span>
                                </li>
                                @if($item->effects)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-magic me-2 text-muted"></i> Efectos:</span>
                                    <span class="fw-bold">{{ $item->effects ? 'Sí' : 'No' }}</span>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($item->effects)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Efectos</h2>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach(json_decode($item->effects, true) as $effect => $value)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $effect }}:</span>
                            <span class="fw-bold">{{ $value }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            
            <!-- Recomendaciones -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">También te puede interesar</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted">Explora más artículos relacionados</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('store.category', $item->category) }}" class="btn btn-outline-primary">
                            <i class="fas fa-th-list me-1"></i> Ver todos los {{ strtolower(ucfirst($item->category)) }}s
                        </a>
                        <a href="{{ route('store.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-store me-1"></i> Volver a la tienda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 