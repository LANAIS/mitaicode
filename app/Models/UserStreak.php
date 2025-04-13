<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserStreak extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'last_activity_date',
        'current_streak',
        'longest_streak',
        'has_activity_today',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_activity_date' => 'date',
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'has_activity_today' => 'boolean',
    ];

    /**
     * Obtener el usuario al que pertenece esta racha.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Registra una actividad diaria para mantener o incrementar la racha.
     * 
     * @return bool True si se actualizó la racha, false si ya hay una actividad registrada hoy
     */
    public function registerActivity(): bool
    {
        $today = Carbon::today();
        
        // Si ya tiene actividad hoy, no hacer nada
        if ($this->has_activity_today && $this->last_activity_date->isToday()) {
            return false;
        }
        
        // Verificar si la última actividad fue ayer para mantener la racha
        if ($this->last_activity_date->isYesterday()) {
            $this->current_streak += 1;
            
            // Actualizar la racha más larga si es necesario
            if ($this->current_streak > $this->longest_streak) {
                $this->longest_streak = $this->current_streak;
            }
        } elseif (!$this->last_activity_date->isToday()) {
            // Si no fue ayer ni hoy, se rompe la racha
            $this->current_streak = 1;
        }
        
        $this->last_activity_date = $today;
        $this->has_activity_today = true;
        $this->save();
        
        return true;
    }

    /**
     * Verifica rachas diarias y reinicia las que no tuvieron actividad.
     * Este método debe ejecutarse una vez al día mediante un trabajo programado.
     */
    public static function processStreaks(): void
    {
        $yesterday = Carbon::yesterday();
        
        // Obtener todas las rachas activas que no tuvieron actividad ayer
        $inactiveStreaks = self::where('has_activity_today', true)
            ->where('last_activity_date', '<', $yesterday)
            ->get();
        
        foreach ($inactiveStreaks as $streak) {
            // Reiniciar la racha
            $streak->current_streak = 0;
            $streak->has_activity_today = false;
            $streak->save();
        }
        
        // Reiniciar el flag de actividad diaria para todos los usuarios
        self::where('has_activity_today', true)
            ->where('last_activity_date', $yesterday)
            ->update(['has_activity_today' => false]);
    }
}
