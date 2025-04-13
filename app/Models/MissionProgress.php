<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionProgress extends Model
{
    use HasFactory;

    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'progress_id';

    /**
     * El nombre de la tabla asociada con el modelo.
     */
    protected $table = 'mission_progress';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'mission_id',
        'status',
        'blocks_used',
        'attempts',
        'completion_time',
        'completed_at',
        'solution_blocks'
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'blocks_used' => 'integer',
        'attempts' => 'integer',
        'completion_time' => 'integer',
        'completed_at' => 'datetime'
    ];

    /**
     * Obtener el usuario al que pertenece este progreso.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Obtener la misión a la que pertenece este progreso.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }
}
