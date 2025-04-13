<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromptLesson extends Model
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
        'title',
        'description',
        'content',
        'teacher_id',
        'class_id',
        'is_public',
        'status',
        'difficulty',
        'estimated_time',
        'category_id',
        'points',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content' => 'array',
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el profesor que creó esta lección.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id', 'user_id');
    }

    /**
     * Obtener la clase a la que pertenece esta lección.
     * Puede ser null si es pública para todos.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'class_id', 'class_id');
    }

    /**
     * Obtener los ejercicios asociados a esta lección.
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(PromptExercise::class, 'lesson_id');
    }

    /**
     * Obtener los avances de los estudiantes en esta lección.
     */
    public function studentProgress(): HasMany
    {
        return $this->hasMany(PromptLessonProgress::class, 'lesson_id');
    }

    /**
     * Obtener los entregables de los estudiantes relacionados con esta lección.
     */
    public function deliverables(): HasMany
    {
        return $this->hasMany(PromptDeliverable::class, 'lesson_id');
    }

    /**
     * Obtener la categoría a la que pertenece esta lección.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Filtrar lecciones por profesor.
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Filtrar lecciones públicas.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Filtrar lecciones de una clase específica.
     */
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }
} 