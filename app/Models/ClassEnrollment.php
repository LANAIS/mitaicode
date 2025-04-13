<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassEnrollment extends Model
{
    use HasFactory;

    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'enrollment_id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_id',
        'student_id',
        'joined_at',
        'is_active'
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joined_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Obtener la clase a la que pertenece esta inscripción.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'class_id', 'class_id');
    }

    /**
     * Obtener el estudiante al que pertenece esta inscripción.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }
}
