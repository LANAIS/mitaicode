<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\GamificationService;

class ChallengeStudentProgress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'challenge_student_progress';

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed_exercises' => 'integer',
        'total_exercises' => 'integer',
        'score' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the challenge that this progress record belongs to.
     */
    public function challenge()
    {
        return $this->belongsTo(TeachingChallenge::class, 'challenge_id', 'id');
    }

    /**
     * Get the student that this progress record belongs to.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Calculate the completion percentage.
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->total_exercises <= 0) {
            return 0;
        }
        
        return min(100, round(($this->completed_exercises / $this->total_exercises) * 100));
    }

    /**
     * Check if the challenge is completed.
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the challenge is in progress.
     */
    public function getIsInProgressAttribute()
    {
        return $this->status === 'in_progress';
    }

    /**
     * Update the last activity timestamp.
     */
    public function updateActivity()
    {
        $this->last_activity_at = now();
        $this->save();
    }

    /**
     * Mark the challenge as started.
     */
    public function markAsStarted()
    {
        if ($this->status === 'not_started') {
            $this->status = 'in_progress';
            $this->started_at = now();
            $this->last_activity_at = now();
            $this->save();
        }
    }

    /**
     * Mark the challenge as completed.
     */
    public function markAsCompleted($score = null)
    {
        $wasCompleted = $this->status !== 'completed';
        
        $this->status = 'completed';
        $this->completed_at = now();
        $this->last_activity_at = now();
        
        if ($score !== null) {
            $this->score = $score;
        }
        
        $this->save();
        
        // Si el desafío acaba de ser completado, registrar en el sistema de gamificación
        if ($wasCompleted) {
            // Calcular puntos en base al score o asignar puntos predeterminados
            $points = $this->score > 0 ? $this->score : 100;
            
            // Registrar la actividad en el sistema de gamificación
            try {
                $gamificationService = app(GamificationService::class);
                $gamificationService->registerActivity(
                    $this->student_id,
                    $points,
                    ['completed_challenges' => 1]
                );
            } catch (\Exception $e) {
                // Registrar el error pero permitir que la función continúe
                \Illuminate\Support\Facades\Log::error('Error al registrar puntos de gamificación: ' . $e->getMessage());
            }
        }
    }

    /**
     * Update the progress of completed exercises.
     */
    public function updateProgress($completedCount)
    {
        $wasNotCompleted = $this->status !== 'completed';
        $this->completed_exercises = $completedCount;
        $this->last_activity_at = now();
        
        if ($this->completed_exercises >= $this->total_exercises && $wasNotCompleted) {
            $this->markAsCompleted();
        } else if ($this->status === 'not_started' && $this->completed_exercises > 0) {
            $this->markAsStarted();
        } else {
            $this->save();
        }
        
        // Si se completó un nuevo ejercicio pero no todo el desafío, registrar en gamificación
        if ($wasNotCompleted && $this->completed_exercises > 0 && $this->completed_exercises < $this->total_exercises) {
            try {
                $gamificationService = app(GamificationService::class);
                $gamificationService->registerActivity(
                    $this->student_id,
                    10, // Puntos por completar un ejercicio individual
                    ['completed_exercises' => 1]
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error al registrar puntos de ejercicio: ' . $e->getMessage());
            }
        }
    }
}
