<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachingChallenge extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'teaching_challenges';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'objectives',
        'instructions',
        'teacher_id',
        'class_id',
        'is_public',
        'status',
        'challenge_type',
        'difficulty',
        'estimated_time',
        'points',
        'order',
        'evaluation_criteria',
        'solution_guide',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'evaluation_criteria' => 'array',
        'estimated_time' => 'integer',
        'points' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the teacher that owns the challenge.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'user_id');
    }

    /**
     * Get the class that the challenge belongs to.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id', 'class_id');
    }

    /**
     * Get the exercises for the challenge.
     */
    public function exercises()
    {
        return $this->hasMany(ChallengeExercise::class, 'challenge_id', 'id');
    }

    /**
     * Get the student progress records for this challenge.
     */
    public function studentProgress()
    {
        return $this->hasMany(ChallengeStudentProgress::class, 'challenge_id', 'id');
    }

    /**
     * Get the analytics for this challenge.
     */
    public function analytics()
    {
        return $this->hasOne(ChallengeAnalytic::class, 'challenge_id', 'id');
    }

    /**
     * Scope a query to only include challenges of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('challenge_type', $type);
    }

    /**
     * Scope a query to only include challenges with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include public challenges.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include challenges for a specific class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Check if a student has started this challenge.
     */
    public function isStartedBy($studentId)
    {
        return $this->studentProgress()
            ->where('student_id', $studentId)
            ->whereIn('status', ['in_progress', 'completed'])
            ->exists();
    }

    /**
     * Check if a student has completed this challenge.
     */
    public function isCompletedBy($studentId)
    {
        return $this->studentProgress()
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->exists();
    }
}
