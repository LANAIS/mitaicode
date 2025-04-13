<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeAnalytic extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'challenge_analytics';

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
        'total_students',
        'started_count',
        'completed_count',
        'average_score',
        'average_time_minutes',
        'completion_by_day',
        'difficulty_metrics',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_students' => 'integer',
        'started_count' => 'integer',
        'completed_count' => 'integer',
        'average_score' => 'float',
        'average_time_minutes' => 'integer',
        'completion_by_day' => 'array',
        'difficulty_metrics' => 'array',
    ];

    /**
     * Get the challenge that these analytics belong to.
     */
    public function challenge()
    {
        return $this->belongsTo(TeachingChallenge::class, 'challenge_id', 'id');
    }

    /**
     * Calculate the completion rate.
     */
    public function getCompletionRateAttribute()
    {
        if ($this->started_count <= 0) {
            return 0;
        }
        
        return round(($this->completed_count / $this->started_count) * 100, 2);
    }

    /**
     * Calculate the engagement rate.
     */
    public function getEngagementRateAttribute()
    {
        if ($this->total_students <= 0) {
            return 0;
        }
        
        return round(($this->started_count / $this->total_students) * 100, 2);
    }

    /**
     * Update the analytics based on student progress.
     */
    public function updateStats()
    {
        if (!$this->challenge) {
            // Aseguramos que siempre devuelva el objeto, incluso si falla
            return false;
        }
        
        $studentProgress = $this->challenge->studentProgress;
        
        // Actualizar conteos básicos
        $this->total_students = $studentProgress->count();
        $this->started_count = $studentProgress->whereIn('status', ['in_progress', 'completed'])->count();
        $this->completed_count = $studentProgress->where('status', 'completed')->count();
        
        // Calcular promedio de puntaje
        $completedProgress = $studentProgress->where('status', 'completed');
        $this->average_score = $completedProgress->count() > 0 
            ? $completedProgress->avg('score') 
            : 0;
        
        // Calcular tiempo promedio para completar (en minutos)
        $completedWithTime = $completedProgress->filter(function ($progress) {
            return $progress->started_at && $progress->completed_at;
        });
        
        if ($completedWithTime->count() > 0) {
            $totalMinutes = 0;
            foreach ($completedWithTime as $progress) {
                $totalMinutes += $progress->completed_at->diffInMinutes($progress->started_at);
            }
            $this->average_time_minutes = round($totalMinutes / $completedWithTime->count());
        }
        
        // Datos de finalización por día (para gráficos)
        $completionByDay = [];
        foreach ($completedProgress as $progress) {
            $day = $progress->completed_at->format('Y-m-d');
            if (!isset($completionByDay[$day])) {
                $completionByDay[$day] = 0;
            }
            $completionByDay[$day]++;
        }
        $this->completion_by_day = $completionByDay;
        
        // Métricas de dificultad basadas en intentos promedio
        // Este sería un cálculo más complejo basado en los intentos de los estudiantes
        // para completar los ejercicios del desafío
        
        $this->save();
        return true;
    }

    /**
     * Create or update analytics for a challenge.
     */
    public static function updateForChallenge($challengeId)
    {
        $analytics = self::firstOrNew(['challenge_id' => $challengeId]);
        $analytics->updateStats();
        return $analytics;
    }
}
