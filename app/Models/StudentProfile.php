<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    use HasFactory;

    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'profile_id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'level',
        'xp_points',
        'total_blocks_used',
        'total_missions_completed',
        'parent_email',
        'age'
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => 'integer',
        'xp_points' => 'integer',
        'total_blocks_used' => 'integer',
        'total_missions_completed' => 'integer',
        'age' => 'integer'
    ];
    
    /**
     * Los atributos calculados que se agregarán a la serialización.
     *
     * @var array
     */
    protected $appends = ['total_progress'];

    /**
     * Obtener el usuario al que pertenece este perfil.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    
    /**
     * Calcular el progreso total del estudiante.
     * 
     * @return int
     */
    public function getTotalProgressAttribute(): int
    {
        // Aquí puedes implementar la lógica real de cálculo del progreso
        // Por ahora, usaremos un valor de ejemplo basado en XP
        if ($this->total_missions_completed > 0) {
            // Por ejemplo, asumimos que 100 XP es 10% de progreso
            $progress = min(100, round(($this->xp_points / 1000) * 100));
            return $progress;
        }
        
        return 0;
    }
    
    /**
     * Calcula el nivel basado en los puntos XP.
     * La fórmula se basa en una curva de dificultad progresiva.
     * 
     * @param int $xp Los puntos XP para calcular el nivel
     * @return int El nivel calculado
     */
    public static function calculateLevel(int $xp): int
    {
        // Fórmula de nivel: Nivel = 1 + floor(sqrt(XP / 100))
        // Esto hace que se necesiten cada vez más puntos para subir de nivel:
        // Nivel 1: 0-99 XP
        // Nivel 2: 100-399 XP
        // Nivel 3: 400-899 XP
        // Nivel 4: 900-1599 XP
        // etc.
        return 1 + (int)floor(sqrt($xp / 100));
    }
    
    /**
     * Agrega puntos XP al estudiante y actualiza su nivel automáticamente.
     * 
     * @param int $points Los puntos XP a agregar
     * @return array Un array con información del cambio: ['old_level', 'new_level', 'level_up', 'new_xp']
     */
    public function addXpPoints(int $points): array
    {
        $oldLevel = $this->level;
        
        // Añadir los puntos
        $this->xp_points += $points;
        
        // Calcular el nuevo nivel
        $newLevel = self::calculateLevel($this->xp_points);
        $this->level = $newLevel;
        
        // Guardar los cambios
        $this->save();
        
        return [
            'old_level' => $oldLevel,
            'new_level' => $newLevel,
            'level_up' => $newLevel > $oldLevel,
            'new_xp' => $this->xp_points
        ];
    }
}
