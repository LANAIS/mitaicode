<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    use HasFactory;

    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'user_badge_id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at'
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'earned_at' => 'datetime'
    ];

    /**
     * Obtener el usuario al que pertenece esta insignia.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Obtener la insignia asociada.
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, 'badge_id', 'badge_id');
    }
}
