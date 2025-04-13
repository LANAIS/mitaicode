@extends('layouts.app')

@section('title', 'Personalizar Avatar - Tienda Mitaí')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('store.inventory') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Volver a mi inventario
        </a>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <h1 class="h3 mb-0">Personalizar Avatar</h1>
        </div>
    </div>
    
    <div class="row">
        <!-- Avatar Preview -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Vista Previa</h2>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-preview mb-3">
                        @if($avatar)
                            <img src="{{ $avatar->getAvatarImageUrl() }}" class="img-fluid rounded-circle avatar-img" 
                            style="width: 200px; height: 200px; border: 5px solid #4e73df;" alt="Avatar">
                        @else
                            <div class="default-avatar rounded-circle d-flex align-items-center justify-content-center bg-light"
                            style="width: 200px; height: 200px; border: 5px solid #4e73df; margin: 0 auto;">
                                <i class="fas fa-user-circle text-primary" style="font-size: 150px;"></i>
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
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> Personaliza tu avatar eligiendo las opciones de la derecha
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customization Options -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Personalización</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('store.avatar.update') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Base Avatar -->
                            <div class="col-md-6">
                                <label for="base_avatar" class="form-label">Estilo Base</label>
                                <select class="form-select" id="base_avatar" name="base_avatar">
                                    <option value="default" {{ $avatar->base_avatar == 'default' ? 'selected' : '' }}>Clásico</option>
                                    <option value="modern" {{ $avatar->base_avatar == 'modern' ? 'selected' : '' }}>Moderno</option>
                                    <option value="pixel" {{ $avatar->base_avatar == 'pixel' ? 'selected' : '' }}>Pixel Art</option>
                                    <option value="cartoon" {{ $avatar->base_avatar == 'cartoon' ? 'selected' : '' }}>Cartoon</option>
                                </select>
                            </div>
                            
                            <!-- Skin Color -->
                            <div class="col-md-6">
                                <label for="skin_color" class="form-label">Color de Piel</label>
                                <input type="color" class="form-control form-control-color w-100" id="skin_color" name="skin_color" value="{{ $avatar->skin_color }}">
                            </div>
                            
                            <!-- Hair Style -->
                            <div class="col-md-6">
                                <label for="hair_style" class="form-label">Peinado</label>
                                <select class="form-select" id="hair_style" name="hair_style">
                                    <option value="default" {{ $avatar->hair_style == 'default' ? 'selected' : '' }}>Clásico</option>
                                    <option value="long" {{ $avatar->hair_style == 'long' ? 'selected' : '' }}>Largo</option>
                                    <option value="short" {{ $avatar->hair_style == 'short' ? 'selected' : '' }}>Corto</option>
                                    <option value="curly" {{ $avatar->hair_style == 'curly' ? 'selected' : '' }}>Rizado</option>
                                    <option value="afro" {{ $avatar->hair_style == 'afro' ? 'selected' : '' }}>Afro</option>
                                </select>
                            </div>
                            
                            <!-- Hair Color -->
                            <div class="col-md-6">
                                <label for="hair_color" class="form-label">Color de Pelo</label>
                                <input type="color" class="form-control form-control-color w-100" id="hair_color" name="hair_color" value="{{ $avatar->hair_color }}">
                            </div>
                            
                            <!-- Eye Type -->
                            <div class="col-md-6">
                                <label for="eye_type" class="form-label">Tipo de Ojos</label>
                                <select class="form-select" id="eye_type" name="eye_type">
                                    <option value="default" {{ $avatar->eye_type == 'default' ? 'selected' : '' }}>Clásicos</option>
                                    <option value="round" {{ $avatar->eye_type == 'round' ? 'selected' : '' }}>Redondos</option>
                                    <option value="almond" {{ $avatar->eye_type == 'almond' ? 'selected' : '' }}>Almendrados</option>
                                    <option value="anime" {{ $avatar->eye_type == 'anime' ? 'selected' : '' }}>Anime</option>
                                </select>
                            </div>
                            
                            <!-- Eye Color -->
                            <div class="col-md-6">
                                <label for="eye_color" class="form-label">Color de Ojos</label>
                                <input type="color" class="form-control form-control-color w-100" id="eye_color" name="eye_color" value="{{ $avatar->eye_color }}">
                            </div>
                            
                            <!-- Mouth Type -->
                            <div class="col-md-6">
                                <label for="mouth_type" class="form-label">Tipo de Boca</label>
                                <select class="form-select" id="mouth_type" name="mouth_type">
                                    <option value="default" {{ $avatar->mouth_type == 'default' ? 'selected' : '' }}>Clásica</option>
                                    <option value="smile" {{ $avatar->mouth_type == 'smile' ? 'selected' : '' }}>Sonrisa</option>
                                    <option value="serious" {{ $avatar->mouth_type == 'serious' ? 'selected' : '' }}>Seria</option>
                                    <option value="cute" {{ $avatar->mouth_type == 'cute' ? 'selected' : '' }}>Tierna</option>
                                </select>
                            </div>
                            
                            <!-- Outfit -->
                            <div class="col-md-6">
                                <label for="outfit" class="form-label">Atuendo</label>
                                <select class="form-select" id="outfit" name="outfit">
                                    <option value="default" {{ $avatar->outfit == 'default' ? 'selected' : '' }}>Clásico</option>
                                    <option value="casual" {{ $avatar->outfit == 'casual' ? 'selected' : '' }}>Casual</option>
                                    <option value="formal" {{ $avatar->outfit == 'formal' ? 'selected' : '' }}>Formal</option>
                                    <option value="student" {{ $avatar->outfit == 'student' ? 'selected' : '' }}>Estudiante</option>
                                    <option value="coder" {{ $avatar->outfit == 'coder' ? 'selected' : '' }}>Programador</option>
                                </select>
                            </div>
                            
                            <!-- Accessory -->
                            <div class="col-md-6">
                                <label for="accessory" class="form-label">Accesorio</label>
                                <select class="form-select" id="accessory" name="accessory">
                                    <option value="">Ninguno</option>
                                    <option value="cartoon_glasses" {{ $avatar->accessory == 'cartoon_glasses' ? 'selected' : '' }}>Gafas</option>
                                    <option value="pixel_glasses" {{ $avatar->accessory == 'pixel_glasses' ? 'selected' : '' }}>Gafas Pixeladas</option>
                                    <option value="cap" {{ $avatar->accessory == 'cap' ? 'selected' : '' }}>Gorra</option>
                                    <option value="headphones" {{ $avatar->accessory == 'headphones' ? 'selected' : '' }}>Auriculares</option>
                                    <option value="bowtie" {{ $avatar->accessory == 'bowtie' ? 'selected' : '' }}>Pajarita</option>
                                    <option value="facemask" {{ $avatar->accessory == 'facemask' ? 'selected' : '' }}>Mascarilla</option>
                                    <option value="scarf" {{ $avatar->accessory == 'scarf' ? 'selected' : '' }}>Bufanda</option>
                                    <option value="crown" {{ $avatar->accessory == 'crown' ? 'selected' : '' }}>Corona</option>
                                    <option value="necklace" {{ $avatar->accessory == 'necklace' ? 'selected' : '' }}>Collar</option>
                                </select>
                            </div>
                            
                            <!-- Background -->
                            <div class="col-md-6">
                                <label for="background" class="form-label">Fondo</label>
                                <select class="form-select" id="background" name="background">
                                    <option value="default" {{ $avatar->background == 'default' ? 'selected' : '' }}>Clásico</option>
                                    <option value="gradient" {{ $avatar->background == 'gradient' ? 'selected' : '' }}>Degradado</option>
                                    <option value="space" {{ $avatar->background == 'space' ? 'selected' : '' }}>Espacio</option>
                                    <option value="code" {{ $avatar->background == 'code' ? 'selected' : '' }}>Código</option>
                                    <option value="mountains" {{ $avatar->background == 'mountains' ? 'selected' : '' }}>Montañas</option>
                                </select>
                            </div>
                            
                            <!-- Frame -->
                            <div class="col-md-6">
                                <label for="frame" class="form-label">Marco</label>
                                <select class="form-select" id="frame" name="frame">
                                    <option value="">Ninguno</option>
                                    <option value="circle" {{ $avatar->frame == 'circle' ? 'selected' : '' }}>Círculo</option>
                                    <option value="hex" {{ $avatar->frame == 'hex' ? 'selected' : '' }}>Hexágono</option>
                                    <option value="diamond" {{ $avatar->frame == 'diamond' ? 'selected' : '' }}>Diamante</option>
                                    <option value="gold" {{ $avatar->frame == 'gold' ? 'selected' : '' }}>Dorado</option>
                                </select>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                                <a href="{{ route('store.inventory') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Items desbloqueados -->
            @if(isset($avatarItems) && $avatarItems->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Items Desbloqueados</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted">Estos son los items especiales que has desbloqueado para tu avatar:</p>
                    
                    <div class="row g-3">
                        @foreach($avatarItems as $type => $items)
                            <div class="col-12">
                                <h3 class="h6 mb-2">{{ ucfirst($type) }}</h3>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($items as $item)
                                        <div class="unlocked-item p-2 border rounded {{ $item->is_equipped ? 'border-success bg-light' : '' }}"
                                             style="width: 100px; transition: all 0.3s ease;">
                                            @if($item->item->image_path)
                                                <img src="{{ asset($item->item->image_path) }}" 
                                                     class="img-fluid mb-1" alt="{{ $item->item->name }}"
                                                     style="height: 60px; width: 60px; object-fit: contain; display: block; margin: 0 auto;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center mb-1"
                                                     style="height: 60px; width: 60px; margin: 0 auto;">
                                                    <i class="fas fa-user-circle text-primary" style="font-size: 50px;"></i>
                                                </div>
                                            @endif
                                            <p class="small text-center mb-1" style="font-size: 0.75rem; line-height: 1;">{{ $item->item->name }}</p>
                                            
                                            @if($item->is_equipped)
                                                <form action="{{ route('store.unequip', $item->inventory_id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100" style="font-size: 0.7rem; padding: 0.1rem 0.25rem;">
                                                        Quitar
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('store.equip', $item->inventory_id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100" style="font-size: 0.7rem; padding: 0.1rem 0.25rem;">
                                                        Usar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('store.category', 'avatar') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-shopping-cart me-1"></i> Comprar más items
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .avatar-img {
        transition: all 0.3s ease;
        object-fit: cover;
    }
    
    .avatar-img:hover {
        transform: scale(1.05);
    }
    
    .unlocked-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
    }
    
    .form-control-color {
        height: 38px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos de formulario
    const formElements = {
        base_avatar: document.getElementById('base_avatar'),
        skin_color: document.getElementById('skin_color'),
        hair_style: document.getElementById('hair_style'),
        hair_color: document.getElementById('hair_color'),
        eye_type: document.getElementById('eye_type'),
        eye_color: document.getElementById('eye_color'),
        mouth_type: document.getElementById('mouth_type'),
        outfit: document.getElementById('outfit'),
        accessory: document.getElementById('accessory'),
        background: document.getElementById('background'),
        frame: document.getElementById('frame')
    };
    
    // Referencia a la imagen del avatar
    const avatarImg = document.querySelector('.avatar-img');
    
    // Estado actual de las opciones
    let currentOptions = {};
    
    // Inicialización - Guardar valores actuales
    for (const [key, element] of Object.entries(formElements)) {
        if (element) {
            currentOptions[key] = element.value;
            
            // Agregar listener para detectar cambios
            element.addEventListener('change', function() {
                currentOptions[key] = this.value;
                previewAvatarChanges();
            });
            
            // Para los campos de color, también escuchar el evento input
            if (element.type === 'color') {
                element.addEventListener('input', function() {
                    currentOptions[key] = this.value;
                    previewAvatarChanges();
                });
            }
        }
    }
    
    // Función para previsualizar los cambios sin guardar
    function previewAvatarChanges() {
        // Construir la URL para la API de previsualización del avatar
        const previewUrl = `/api/avatar/preview?` + 
            Object.entries(currentOptions)
                .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
                .join('&');
        
        // Mostrar un indicador de carga
        avatarImg.classList.add('opacity-50');
        
        // Realizar la solicitud al backend
        fetch(previewUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al generar la previsualización');
                }
                return response.json();
            })
            .then(data => {
                if (data.avatar_url) {
                    // Actualizar la imagen con un parámetro de caché para evitar que el navegador la almacene en caché
                    avatarImg.src = data.avatar_url + '?t=' + new Date().getTime();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                // Quitar el indicador de carga
                avatarImg.classList.remove('opacity-50');
            });
    }
});
</script>
@endsection 