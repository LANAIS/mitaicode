<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromptExercise extends Model
{
    use HasFactory;

    /**
     * La clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'instructions',
        'example_prompt',
        'hint',
        'evaluation_criteria',
        'order',
        'points',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'evaluation_criteria' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener la lección a la que pertenece este ejercicio.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(PromptLesson::class, 'lesson_id');
    }

    /**
     * Obtener las resoluciones de los estudiantes para este ejercicio.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(PromptSubmission::class, 'exercise_id');
    }

    /**
     * Ordenar los ejercicios por su orden dentro de la lección.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
} 