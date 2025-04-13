<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    use HasFactory;

    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'badge_id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'badge_code',
        'name',
        'description',
        'image_url',
        'criteria'
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime'
    ];

    /**
     * Obtener los usuarios que tienen esta insignia.
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class, 
            'user_badges', 
            'badge_id', 
            'user_id', 
            'badge_id', 
            'user_id'
        );
    }

    /**
     * Obtener las asignaciones de esta insignia a usuarios.
     */
    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class, 'badge_id', 'badge_id');
    }
}
