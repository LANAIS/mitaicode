<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreItem extends Model
{
    use HasFactory;
    
    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'item_id';
    
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'category',
        'type',
        'price',
        'level_required',
        'is_limited',
        'stock',
        'effects',
        'is_active',
        'purchases',
    ];
    
    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'integer',
        'level_required' => 'integer',
        'is_limited' => 'boolean',
        'stock' => 'integer',
        'effects' => 'json',
        'is_active' => 'boolean',
        'purchases' => 'integer',
    ];
    
    /**
     * Obtener las instancias de este item en los inventarios de usuarios.
     */
    public function userInventories(): HasMany
    {
        return $this->hasMany(UserInventory::class, 'item_id', 'item_id');
    }
    
    /**
     * Verificar si un item está disponible para compra.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->is_limited && $this->stock <= 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Verificar si un usuario puede comprar este item.
     *
     * @param User $user
     * @return bool
     */
    public function canBePurchasedBy(User $user): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        // Verificar si el usuario tiene suficientes puntos
        if ($user->studentProfile && $user->studentProfile->xp_points < $this->price) {
            return false;
        }
        
        // Verificar si el usuario tiene el nivel requerido
        if ($user->studentProfile && $user->studentProfile->level < $this->level_required) {
            return false;
        }
        
        // Verificar si el usuario ya tiene este ítem
        $hasItem = UserInventory::where('user_id', $user->user_id)
            ->where('item_id', $this->item_id)
            ->exists();
            
        if ($hasItem) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Buscar items por categoría.
     *
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findByCategory(string $category)
    {
        return self::where('category', $category)
            ->where('is_active', true)
            ->orderBy('level_required')
            ->orderBy('price')
            ->get();
    }
}
