<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardEntry extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'leaderboard_id',
        'user_id',
        'score',
        'streak',
        'completed_challenges',
        'completed_exercises',
        'ranking_position',
        'position',
        'metadata',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'integer',
        'streak' => 'integer',
        'completed_challenges' => 'integer',
        'completed_exercises' => 'integer',
        'ranking_position' => 'integer',
        'position' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Obtener la tabla de puntajes a la que pertenece esta entrada.
     */
    public function leaderboard(): BelongsTo
    {
        return $this->belongsTo(Leaderboard::class);
    }

    /**
     * Obtener el usuario asociado a esta entrada.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Incrementar el puntaje de la entrada.
     *
     * @param int $points Puntos a añadir
     * @return $this
     */
    public function addPoints(int $points): self
    {
        $this->score += $points;
        $this->save();
        
        // Actualizar posiciones en el ranking
        $this->leaderboard->updateRankings();
        
        return $this;
    }

    /**
     * Incrementar el contador de desafíos completados.
     *
     * @param int $count Cantidad a incrementar (por defecto 1)
     * @return $this
     */
    public function incrementChallenges(int $count = 1): self
    {
        $this->completed_challenges += $count;
        $this->save();
        
        return $this;
    }

    /**
     * Incrementar el contador de ejercicios completados.
     *
     * @param int $count Cantidad a incrementar (por defecto 1)
     * @return $this
     */
    public function incrementExercises(int $count = 1): self
    {
        $this->completed_exercises += $count;
        $this->save();
        
        return $this;
    }

    /**
     * Actualizar la racha actual.
     *
     * @param int $streak Nueva racha
     * @return $this
     */
    public function updateStreak(int $streak): self
    {
        $this->streak = $streak;
        $this->save();
        
        return $this;
    }
}
