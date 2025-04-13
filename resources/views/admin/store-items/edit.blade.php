@extends('layouts.admin')

@section('title', 'Editar Item - Panel de Administración')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Item: {{ $item->name }}</h1>
        <a href="{{ route('admin.store-items.index') }}" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Volver a la lista
        </a>
    </div>

    <!-- Tarjeta principal -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detalles del Item</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.store-items.update', $item) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-lg-6">
                        <!-- Nombre -->
                        <div class="form-group">
                            <label for="name">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Descripción -->
                        <div class="form-group">
                            <label for="description">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $item->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Describe el item de forma detallada. Esta descripción se mostrará a los usuarios.</small>
                        </div>
                        
                        <!-- Imagen -->
                        <div class="form-group">
                            <label for="image">Imagen</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                <label class="custom-file-label" for="image">Seleccionar archivo nuevo...</label>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Deja vacío para mantener la imagen actual. Recomendado: 512x512px, formato PNG con fondo transparente.</small>
                        </div>
                        
                        <div class="form-row">
                            <!-- Categoría -->
                            <div class="form-group col-md-6">
                                <label for="category">Categoría <span class="text-danger">*</span></label>
                                <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="avatar" {{ old('category', $item->category) == 'avatar' ? 'selected' : '' }}>Avatar</option>
                                    <option value="badge" {{ old('category', $item->category) == 'badge' ? 'selected' : '' }}>Insignia</option>
                                    <option value="rank" {{ old('category', $item->category) == 'rank' ? 'selected' : '' }}>Rango</option>
                                    <option value="skin" {{ old('category', $item->category) == 'skin' ? 'selected' : '' }}>Tema</option>
                                    <option value="special" {{ old('category', $item->category) == 'special' ? 'selected' : '' }}>Especial</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Tipo -->
                            <div class="form-group col-md-6">
                                <label for="type">Tipo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('type') is-invalid @enderror" id="type" name="type" value="{{ old('type', $item->type) }}" required>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted" id="type-help">Ejemplos: accessory, appearance, title, decoration, boost</small>
                            </div>
                        </div>
                        
                        <!-- Slug -->
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" value="{{ $item->slug }}" disabled readonly>
                            <small class="form-text text-muted">El slug no se puede modificar para mantener la consistencia de las URLs.</small>
                        </div>
                    </div>
                    
                    <!-- Columna derecha -->
                    <div class="col-lg-6">
                        <div class="form-row">
                            <!-- Precio -->
                            <div class="form-group col-md-6">
                                <label for="price">Precio (gemas) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $item->price) }}" min="0" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-gem text-primary"></i></span>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Nivel requerido -->
                            <div class="form-group col-md-6">
                                <label for="level_required">Nivel requerido <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('level_required') is-invalid @enderror" id="level_required" name="level_required" value="{{ old('level_required', $item->level_required) }}" min="1" required>
                                @error('level_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Es limitado / Stock -->
                        <div class="form-group">
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="is_limited" name="is_limited" {{ old('is_limited', $item->is_limited) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_limited">Item de cantidad limitada</label>
                            </div>
                            <div id="stock-container" class="{{ old('is_limited', $item->is_limited) ? '' : 'd-none' }}">
                                <label for="stock">Stock disponible</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $item->stock) }}" min="1">
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Estado -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Item activo y disponible para compra</label>
                            </div>
                        </div>
                        
                        <!-- Efectos (para items especiales) -->
                        <div class="form-group" id="effects-container" style="{{ $item->category !== 'special' ? 'display: none;' : '' }}">
                            <label for="effects">Efectos (JSON)</label>
                            <textarea class="form-control @error('effects') is-invalid @enderror" id="effects" name="effects" rows="4">{{ old('effects', $item->effects) }}</textarea>
                            @error('effects')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Solo para items especiales. Formato JSON con propiedades adicionales.</small>
                        </div>
                        
                        <!-- Imagen actual -->
                        <div class="form-group d-flex justify-content-center mt-3">
                            <div class="text-center">
                                <div id="image-preview" class="mb-2" style="width: 150px; height: 150px; border: 2px dashed #ccc; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    @if($item->image_path)
                                        <img src="{{ asset($item->image_path) }}" class="img-fluid" style="max-height: 150px; max-width: 150px;">
                                    @else
                                        <i class="fas fa-image fa-3x text-gray-300"></i>
                                    @endif
                                </div>
                                <small class="text-muted">Imagen actual</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Botones -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('admin.store-items.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Mostrar nombre del archivo seleccionado
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
            
            // Vista previa de la imagen
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').html('<img src="' + e.target.result + '" class="img-fluid" style="max-height: 150px; max-width: 150px;">');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Mostrar/ocultar campo de stock según si es limitado
        $('#is_limited').on('change', function() {
            if ($(this).is(':checked')) {
                $('#stock-container').removeClass('d-none');
            } else {
                $('#stock-container').addClass('d-none');
            }
        });
        
        // Cambiar ayuda del tipo según la categoría seleccionada
        $('#category').on('change', function() {
            const category = $(this).val();
            let typeHelp = 'Ejemplos: ';
            
            switch(category) {
                case 'avatar':
                    typeHelp += 'accessory, appearance, outfit, background';
                    break;
                case 'badge':
                    typeHelp += 'decoration, achievement, special';
                    break;
                case 'rank':
                    typeHelp += 'title, level, rank';
                    break;
                case 'skin':
                    typeHelp += 'appearance, theme, style';
                    break;
                case 'special':
                    typeHelp += 'boost, power, ability';
                    $('#effects-container').show();
                    break;
                default:
                    typeHelp += 'accessory, appearance, title, decoration, boost';
            }
            
            $('#type-help').text(typeHelp);
            
            // Mostrar/ocultar campo de efectos solo para items especiales
            if (category === 'special') {
                $('#effects-container').show();
            } else {
                $('#effects-container').hide();
            }
        });
    });
</script>
@endsection 