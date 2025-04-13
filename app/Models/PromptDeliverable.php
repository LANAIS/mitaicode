<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptDeliverable extends Model
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
        'title',
        'description',
        'file_path',
        'content',
        'grade',
        'feedback',
        'status',
        'submitted_at',
        'graded_at',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'grade' => 'float',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener la lección a la que pertenece este entregable.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(PromptLesson::class, 'lesson_id');
    }

    /**
     * Obtener el estudiante que subió este entregable.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Filtrar entregables por estudiante.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Filtrar entregables por lección.
     */
    public function scopeByLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    /**
     * Filtrar entregables pendientes de calificación.
     */
    public function scopePendingGrade($query)
    {
        return $query->whereNull('graded_at');
    }

    /**
     * Filtrar entregables calificados.
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('graded_at');
    }
} 