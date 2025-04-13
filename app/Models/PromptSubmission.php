<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptSubmission extends Model
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
        'exercise_id',
        'student_id',
        'prompt_text',
        'ai_response',
        'score',
        'feedback',
        'status',
        'attempt_number',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el ejercicio al que pertenece esta resolución.
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(PromptExercise::class, 'exercise_id');
    }

    /**
     * Obtener el estudiante que realizó esta resolución.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Obtener el progreso general de la lección a la que pertenece este ejercicio.
     */
    public function lessonProgress(): BelongsTo
    {
        return $this->belongsTo(PromptLessonProgress::class, 
            ['exercise_id', 'student_id'], 
            ['exercise_id', 'student_id']);
    }

    /**
     * Filtrar resoluciones por estudiante.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Filtrar resoluciones por ejercicio.
     */
    public function scopeByExercise($query, $exerciseId)
    {
        return $query->where('exercise_id', $exerciseId);
    }

    /**
     * Filtrar la última resolución de un estudiante para un ejercicio.
     */
    public function scopeLatestAttempt($query)
    {
        return $query->orderBy('attempt_number', 'desc');
    }
} 