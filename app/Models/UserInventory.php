<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserInventory extends Model
{
    use HasFactory;
    
    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'inventory_id';
    
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'is_equipped',
        'is_used',
        'acquired_at',
        'expires_at',
        'custom_properties',
    ];
    
    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_equipped' => 'boolean',
        'is_used' => 'boolean',
        'acquired_at' => 'datetime',
        'expires_at' => 'datetime',
        'custom_properties' => 'json',
    ];
    
    /**
     * Obtener el usuario propietario del inventario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    
    /**
     * Obtener el item del inventario.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(StoreItem::class, 'item_id', 'item_id');
    }
    
    /**
     * Verificar si el item ha expirado.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }
        
        return $this->expires_at->isPast();
    }
    
    /**
     * Equipar el item.
     *
     * @return bool
     */
    public function equip(): bool
    {
        if ($this->hasExpired()) {
            return false;
        }
        
        // Desequipar otros items de la misma categoría
        $item = $this->item;
        if ($item) {
            UserInventory::where('user_id', $this->user_id)
                ->whereHas('item', function($query) use ($item) {
                    $query->where('category', $item->category);
                })
                ->where('is_equipped', true)
                ->update(['is_equipped' => false]);
        }
        
        $this->is_equipped = true;
        return $this->save();
    }
    
    /**
     * Desequipar el item.
     *
     * @return bool
     */
    public function unequip(): bool
    {
        $this->is_equipped = false;
        return $this->save();
    }
    
    /**
     * Usar el item (para consumibles).
     *
     * @return bool
     */
    public function use(): bool
    {
        if ($this->hasExpired() || $this->is_used) {
            return false;
        }
        
        $this->is_used = true;
        return $this->save();
    }
    
    /**
     * Obtener items equipados por un usuario.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getEquippedByUser(int $userId)
    {
        return self::where('user_id', $userId)
            ->where('is_equipped', true)
            ->whereNull('expires_at')
            ->orWhere('expires_at', '>', Carbon::now())
            ->with('item')
            ->get();
    }
}
