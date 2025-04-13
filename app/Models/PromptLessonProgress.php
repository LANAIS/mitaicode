<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromptLessonProgress extends Model
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
        'student_id',
        'completed_exercises',
        'total_exercises',
        'score',
        'status',
        'started_at',
        'completed_at',
        'last_activity_at',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'integer',
        'completed_exercises' => 'integer',
        'total_exercises' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener la lección a la que pertenece este progreso.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(PromptLesson::class, 'lesson_id');
    }

    /**
     * Obtener el estudiante al que pertenece este progreso.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Obtener las resoluciones de ejercicios asociadas con este progreso de lección.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(PromptSubmission::class, 'student_id', 'student_id')
            ->whereHas('exercise', function($query) {
                $query->where('lesson_id', $this->lesson_id);
            });
    }

    /**
     * Obtener los entregables asociados con este progreso de lección.
     */
    public function deliverables(): HasMany
    {
        return $this->hasMany(PromptDeliverable::class, 'student_id', 'student_id')
            ->where('lesson_id', $this->lesson_id);
    }

    /**
     * Calcular el porcentaje de finalización.
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->total_exercises == 0) return 0;
        return round(($this->completed_exercises / $this->total_exercises) * 100);
    }

    /**
     * Filtrar progreso por estudiante.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Filtrar progreso por lección.
     */
    public function scopeByLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    /**
     * Filtrar lecciones completadas.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Filtrar lecciones en progreso (iniciadas pero no completadas).
     */
    public function scopeInProgress($query)
    {
        return $query->whereNotNull('started_at')->whereNull('completed_at');
    }
} 