<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory;

    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'class_id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_id',
        'class_name',
        'description',
        'class_code',
        'is_active'
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime'
    ];

    /**
     * Obtener el profesor que creó esta clase.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id', 'user_id');
    }

    /**
     * Obtener las inscripciones de esta clase.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class, 'class_id', 'class_id');
    }

    /**
     * Obtener los proyectos asociados a esta clase.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'class_id', 'class_id');
    }

    /**
     * Obtener los estudiantes inscritos en esta clase.
     */
    public function students()
    {
        return $this->belongsToMany(
            User::class, 
            'class_enrollments', 
            'class_id', 
            'student_id', 
            'class_id', 
            'user_id'
        )->where('role', 'student');
    }
    
    /**
     * Accesores para mantener la compatibilidad con los nombres usados en el código
     */
    public function getNameAttribute()
    {
        return $this->class_name;
    }
    
    public function getAccessCodeAttribute()
    {
        return $this->class_code;
    }
    
    /**
     * Mutadores para mantener la compatibilidad con los nombres usados en el código
     */
    public function setNameAttribute($value)
    {
        $this->attributes['class_name'] = $value;
    }
    
    public function setAccessCodeAttribute($value)
    {
        $this->attributes['class_code'] = $value;
    }
}
