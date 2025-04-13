<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAvatar extends Model
{
    use HasFactory;
    
    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'avatar_id';
    
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'base_avatar',
        'skin_color',
        'hair_style',
        'hair_color',
        'eye_type',
        'eye_color',
        'mouth_type',
        'outfit',
        'accessory',
        'background',
        'frame',
        'custom_elements',
        'current_rank',
        'current_title',
    ];
    
    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'custom_elements' => 'json',
    ];
    
    /**
     * Obtener el usuario propietario del avatar.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    
    /**
     * Generar la URL de la imagen del avatar para mostrar.
     *
     * @return string
     */
    public function getAvatarImageUrl(): string
    {
        // Ruta base para los componentes de avatar
        $componentsPath = 'assets/images/avatar-components';
        
        // Verificar si debemos generar un avatar SVG compuesto
        if ($this->base_avatar == 'pixel' || $this->base_avatar == 'cartoon') {
            return $this->generateCompositeAvatar();
        }
        
        // Si llegamos aquí, usamos el método anterior para compatibilidad
        $baseAvatarPath = 'assets/images/avatars/' . $this->base_avatar;
        $pngPath = $baseAvatarPath . '.png';
        $txtPath = $baseAvatarPath . '.txt';
        
        if (file_exists(public_path($pngPath))) {
            return asset($pngPath);
        }
        
        // Si encuentra el placeholder txt, igualmente usaremos un avatar generado
        // pero esto nos confirma que el estilo base es válido
        $isValidStyle = file_exists(public_path($txtPath));
        
        // Si no hay avatar personalizado, generamos uno con UI Avatars basado en las propiedades del avatar
        $name = $this->user ? ($this->user->first_name . '+' . $this->user->last_name) : 'Usuario';
        $skinColor = ltrim($this->skin_color, '#');
        
        // Convertimos el color de piel a un color de fondo adecuado para el avatar
        // y elegimos un color de texto contrastante
        $bgColor = $skinColor;
        $textColor = $this->getContrastColor($skinColor);
        
        // Si es un estilo base válido, lo incluimos en el texto del avatar
        $avatarText = $isValidStyle ? 
                     urlencode($name . '+(' . ucfirst($this->base_avatar) . ')') : 
                     urlencode($name);
        
        return "https://ui-avatars.com/api/?name=" . $avatarText . 
               "&background=" . $bgColor . 
               "&color=" . $textColor . 
               "&size=256&bold=true";
    }
    
    /**
     * Generar un avatar compuesto a partir de componentes visuales
     * 
     * @return string
     */
    private function generateCompositeAvatar(): string
    {
        // Colores
        $skinColorHex = $this->skin_color;
        $hairColorHex = $this->hair_color;
        $eyeColorHex = $this->eye_color;
        
        // Crear un hash único basado en los atributos para usarlo como nombre de archivo
        $avatarHash = md5(json_encode([
            $this->user_id,
            $this->base_avatar,
            $this->skin_color,
            $this->hair_style,
            $this->hair_color,
            $this->eye_type,
            $this->eye_color,
            $this->mouth_type,
            $this->outfit,
            $this->accessory,
            $this->background,
            $this->frame
        ]));
        
        // Ruta para guardar la imagen del avatar generado
        $avatarFilename = 'avatar_' . $avatarHash . '.svg';
        $avatarPath = 'assets/images/avatars/' . $avatarFilename;
        $fullAvatarPath = public_path($avatarPath);
        
        // Si el avatar ya existe, solo devolver la URL
        if (file_exists($fullAvatarPath)) {
            return asset($avatarPath);
        }
        
        // Definir los componentes basados en los atributos del avatar
        $style = $this->base_avatar; // cartoon o pixel
        
        // Iniciamos la creación del SVG
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">';
        
        // 1. Agregar fondo (si existe)
        $backgroundSvg = $this->getComponentSvg('backgrounds', $this->background, $style);
        if ($backgroundSvg) {
            $svg .= $backgroundSvg;
        } else {
            // Fondo por defecto si no hay componente
            $svg .= '<rect width="400" height="400" fill="#f0f0f0" />';
        }
        
        // 2. Agregar base (cabeza) con el color de piel adecuado
        $baseSvg = $this->getComponentSvg('bases', 'default', $style);
        if ($baseSvg) {
            // Reemplazar el placeholder de color de piel
            $baseSvg = str_replace('{{skin_color}}', $skinColorHex, $baseSvg);
            $svg .= $baseSvg;
        }
        
        // 3. Agregar cabello con el color adecuado
        $hairSvg = $this->getComponentSvg('hair', $this->hair_style, $style);
        if ($hairSvg) {
            // Reemplazar el placeholder de color de pelo
            $hairSvg = str_replace('{{hair_color}}', $hairColorHex, $hairSvg);
            $svg .= $hairSvg;
        }
        
        // 4. Agregar ojos con el color adecuado
        $eyesSvg = $this->getComponentSvg('eyes', $this->eye_type, $style);
        if ($eyesSvg) {
            // Reemplazar el placeholder de color de ojos
            $eyesSvg = str_replace('{{eye_color}}', $eyeColorHex, $eyesSvg);
            $svg .= $eyesSvg;
        }
        
        // 5. Agregar boca
        $mouthSvg = $this->getComponentSvg('mouths', $this->mouth_type, $style);
        if ($mouthSvg) {
            $svg .= $mouthSvg;
        }
        
        // 6. Agregar vestimenta
        $outfitSvg = $this->getComponentSvg('outfits', $this->outfit, $style);
        if ($outfitSvg) {
            $svg .= $outfitSvg;
        }
        
        // 7. Agregar accesorio si existe
        if ($this->accessory) {
            $accessorySvg = $this->getComponentSvg('accessories', $this->accessory, $style);
            if ($accessorySvg) {
                $svg .= $accessorySvg;
            }
        }
        
        // 8. Agregar marco si existe
        if ($this->frame) {
            $frameSvg = $this->getComponentSvg('frames', $this->frame, $style);
            if ($frameSvg) {
                $svg .= $frameSvg;
            }
        }
        
        // Cerrar el SVG
        $svg .= '</svg>';
        
        // Crear el directorio si no existe
        $avatarDir = public_path('assets/images/avatars');
        if (!file_exists($avatarDir)) {
            mkdir($avatarDir, 0755, true);
        }
        
        // Guardar el SVG completo
        file_put_contents($fullAvatarPath, $svg);
        
        // Devolver la URL del avatar generado
        return asset($avatarPath);
    }
    
    /**
     * Obtener un componente SVG para el avatar
     * 
     * @param string $category La categoría del componente (bases, hair, eyes, etc)
     * @param string $type El tipo específico del componente
     * @param string $style El estilo (cartoon o pixel)
     * @return string|null
     */
    private function getComponentSvg(string $category, string $type, string $style): ?string
    {
        // Intentar obtener el componente específico para el estilo
        $componentPath = "assets/images/avatar-components/{$category}/{$style}_{$type}.svg";
        $fullPath = public_path($componentPath);
        
        if (file_exists($fullPath)) {
            return file_get_contents($fullPath);
        }
        
        // Si no existe para ese estilo, intentar componente genérico
        $genericPath = "assets/images/avatar-components/{$category}/{$type}.svg";
        $fullGenericPath = public_path($genericPath);
        
        if (file_exists($fullGenericPath)) {
            return file_get_contents($fullGenericPath);
        }
        
        // Si no se encuentra, buscar el default para esa categoría
        $defaultPath = "assets/images/avatar-components/{$category}/default.svg";
        $fullDefaultPath = public_path($defaultPath);
        
        if (file_exists($fullDefaultPath)) {
            return file_get_contents($fullDefaultPath);
        }
        
        // Si no hay ningún componente disponible, devolver null
        return null;
    }
    
    /**
     * Determina si un color de fondo necesita texto claro u oscuro para contraste
     *
     * @param string $hexColor
     * @return string
     */
    private function getContrastColor(string $hexColor): string
    {
        // Convertir el color hexadecimal a RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Calcular la luminancia (percepción de brillo)
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        // Retornar color de texto basado en la luminancia
        return $luminance > 0.5 ? '000000' : 'FFFFFF';
    }
    
    /**
     * Actualizar un elemento del avatar.
     *
     * @param string $element
     * @param string $value
     * @return bool
     */
    public function updateElement(string $element, string $value): bool
    {
        if (!in_array($element, $this->fillable)) {
            return false;
        }
        
        $this->$element = $value;
        return $this->save();
    }
    
    /**
     * Obtener o crear el avatar de un usuario.
     *
     * @param int $userId
     * @return UserAvatar
     */
    public static function getOrCreate(int $userId): self
    {
        $avatar = self::where('user_id', $userId)->first();
        
        if (!$avatar) {
            $avatar = self::create([
                'user_id' => $userId,
                // Los demás campos usarán valores por defecto definidos en la migración
            ]);
        }
        
        return $avatar;
    }
    
    /**
     * Actualizar el rango del usuario.
     *
     * @param string $rank
     * @return bool
     */
    public function updateRank(string $rank): bool
    {
        $this->current_rank = $rank;
        return $this->save();
    }
    
    /**
     * Actualizar el título del usuario.
     *
     * @param string|null $title
     * @return bool
     */
    public function updateTitle(?string $title): bool
    {
        $this->current_title = $title;
        return $this->save();
    }
    
    /**
     * Generar un avatar de previsualización que no se guarda permanentemente
     * 
     * @return string
     */
    public function generatePreviewAvatar(): string
    {
        // Generar un ID único temporal para la previsualización
        $previewId = 'preview_' . uniqid();
        
        // Colores
        $skinColorHex = $this->skin_color;
        $hairColorHex = $this->hair_color;
        $eyeColorHex = $this->eye_color;
        
        // Nombre del archivo temporal
        $avatarFilename = 'avatar_' . $previewId . '.svg';
        $avatarPath = 'assets/images/avatars/preview/' . $avatarFilename;
        $fullAvatarPath = public_path($avatarPath);
        
        // Crear directorio si no existe
        $previewDir = public_path('assets/images/avatars/preview');
        if (!file_exists($previewDir)) {
            mkdir($previewDir, 0755, true);
        }
        
        // Estilo visual
        $style = $this->base_avatar; // cartoon o pixel
        
        // Iniciamos la creación del SVG
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">';
        
        // 1. Agregar fondo
        $backgroundSvg = $this->getComponentSvg('backgrounds', $this->background, $style);
        if ($backgroundSvg) {
            $svg .= $backgroundSvg;
        } else {
            // Fondo por defecto si no hay componente
            $svg .= '<rect width="400" height="400" fill="#f0f0f0" />';
        }
        
        // 2. Agregar base (cabeza) con color de piel
        $baseSvg = $this->getComponentSvg('bases', 'default', $style);
        if ($baseSvg) {
            // Reemplazar el placeholder de color de piel
            $baseSvg = str_replace('{{skin_color}}', $skinColorHex, $baseSvg);
            $svg .= $baseSvg;
        }
        
        // 3. Agregar cabello con color
        $hairSvg = $this->getComponentSvg('hair', $this->hair_style, $style);
        if ($hairSvg) {
            // Reemplazar el placeholder de color de pelo
            $hairSvg = str_replace('{{hair_color}}', $hairColorHex, $hairSvg);
            $svg .= $hairSvg;
        }
        
        // 4. Agregar ojos con color
        $eyesSvg = $this->getComponentSvg('eyes', $this->eye_type, $style);
        if ($eyesSvg) {
            // Reemplazar el placeholder de color de ojos
            $eyesSvg = str_replace('{{eye_color}}', $eyeColorHex, $eyesSvg);
            $svg .= $eyesSvg;
        }
        
        // 5. Agregar boca
        $mouthSvg = $this->getComponentSvg('mouths', $this->mouth_type, $style);
        if ($mouthSvg) {
            $svg .= $mouthSvg;
        }
        
        // 6. Agregar vestimenta
        $outfitSvg = $this->getComponentSvg('outfits', $this->outfit, $style);
        if ($outfitSvg) {
            $svg .= $outfitSvg;
        }
        
        // 7. Agregar accesorio si existe
        if ($this->accessory) {
            $accessorySvg = $this->getComponentSvg('accessories', $this->accessory, $style);
            if ($accessorySvg) {
                $svg .= $accessorySvg;
            }
        }
        
        // 8. Agregar marco si existe
        if ($this->frame) {
            $frameSvg = $this->getComponentSvg('frames', $this->frame, $style);
            if ($frameSvg) {
                $svg .= $frameSvg;
            }
        }
        
        // Cerrar el SVG
        $svg .= '</svg>';
        
        // Guardar el SVG temporal
        file_put_contents($fullAvatarPath, $svg);
        
        // Devolver la URL temporal
        return asset($avatarPath);
    }
}
