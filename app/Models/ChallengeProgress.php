<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeProgress extends Model
{
    use HasFactory;
    
    protected $table = 'challenge_progress';
    
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'challenge_type',
        'level',
        'challenge_number',
        'submitted_code',
        'is_completed',
        'attempts',
        'completed_at',
    ];
    
    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_completed' => 'boolean',
        'attempts' => 'integer',
        'challenge_number' => 'integer',
        'completed_at' => 'datetime',
    ];
    
    /**
     * Obtener el usuario al que pertenece este progreso.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
} 