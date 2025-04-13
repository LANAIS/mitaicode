<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeExercise extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'challenge_exercises';

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
        'challenge_id',
        'title',
        'description',
        'instructions',
        'hints',
        'starter_code',
        'solution_code',
        'example_prompt',
        'test_cases',
        'evaluation_criteria',
        'order',
        'points',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'test_cases' => 'array',
        'evaluation_criteria' => 'array',
        'order' => 'integer',
        'points' => 'integer',
    ];

    /**
     * Get the challenge that owns the exercise.
     */
    public function challenge()
    {
        return $this->belongsTo(TeachingChallenge::class, 'challenge_id', 'id');
    }

    /**
     * Get the submissions for this exercise.
     */
    public function submissions()
    {
        return $this->hasMany(ExerciseSubmission::class, 'exercise_id', 'id');
    }

    /**
     * Get the most recent submission by a specific student.
     */
    public function latestSubmissionByStudent($studentId)
    {
        return $this->submissions()
            ->where('student_id', $studentId)
            ->latest()
            ->first();
    }

    /**
     * Check if a student has completed this exercise.
     */
    public function isCompletedBy($studentId)
    {
        return $this->submissions()
            ->where('student_id', $studentId)
            ->where('status', 'graded')
            ->where('score', '>', 0)
            ->exists();
    }

    /**
     * Get all test cases for this exercise in a structured format.
     */
    public function getFormattedTestCases()
    {
        return $this->test_cases ?? [];
    }

    /**
     * Get hints based on attempt number (progressively shows more).
     */
    public function getHintForAttempt($attemptNumber)
    {
        if (empty($this->hints)) {
            return null;
        }

        $hintsArray = explode('---', $this->hints);
        $index = min($attemptNumber - 1, count($hintsArray) - 1);
        return $index >= 0 ? $hintsArray[$index] : null;
    }
}
