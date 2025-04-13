<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    use HasFactory;

    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'mission_id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'difficulty',
        'category',
        'xp_reward',
        'badge_reward',
        'requirements',
        'success_criteria',
        'starter_blocks',
        'is_active'
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'xp_reward' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime'
    ];

    /**
     * Obtener el progreso de los usuarios en esta misión.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(MissionProgress::class, 'mission_id', 'mission_id');
    }
}
