<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailNotification extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name',
        'subject',
        'content',
        'trigger_event',
        'trigger_days',
        'is_active',
        'send_time',
        'last_sent_at',
        'created_by',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
        'trigger_days' => 'array',
    ];

    /**
     * Obtener el administrador que creó esta notificación.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Obtener todos los usuarios que califican para esta notificación.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getQualifiedUsers()
    {
        // Obtenemos usuarios según el tipo de notificación
        switch ($this->trigger_event) {
            case 'inactive_user':
                // Usuarios inactivos por X días
                $days = $this->trigger_days[0] ?? 7;
                return User::where('last_login_at', '<', now()->subDays($days))
                    ->where('last_login_at', '>', now()->subDays($days * 2)) // Para no enviar múltiples veces
                    ->get();
            
            case 'incomplete_challenge':
                // Usuarios con desafíos incompletos
                return User::whereHas('challengeProgress', function($query) {
                    $query->where('is_completed', false)
                        ->where('created_at', '<', now()->subDays(3));
                })->get();
            
            case 'new_content':
                // Todos los usuarios activos para notificarles nuevo contenido
                return User::where('last_login_at', '>', now()->subDays(30))
                    ->get();
                
            case 'level_reminder':
                // Estudiantes que no han avanzado de nivel en X días
                $days = $this->trigger_days[0] ?? 14;
                return User::whereHas('studentProfile', function($query) use ($days) {
                    $query->whereRaw('updated_at < DATE_SUB(NOW(), INTERVAL ? DAY)', [$days])
                        ->whereRaw('level_updated_at < DATE_SUB(NOW(), INTERVAL ? DAY)', [$days]);
                })->get();
                
            case 'welcome':
                // Usuarios nuevos (registrados en las últimas 24 horas)
                return User::where('created_at', '>', now()->subDay())
                    ->get();
                
            default:
                return collect();
        }
    }

    /**
     * Obtener todas las notificaciones de email activas.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActive()
    {
        return self::where('is_active', true)->get();
    }

    /**
     * Comprobar si esta notificación debe enviarse hoy.
     *
     * @return bool
     */
    public function shouldSendToday()
    {
        // Comprobar si ya se envió hoy
        if ($this->last_sent_at && $this->last_sent_at->isToday()) {
            return false;
        }

        // Si no tiene días específicos, se envía todos los días
        if (empty($this->trigger_days)) {
            return true;
        }

        // Si tiene días específicos, se envía solo en esos días
        $today = now()->dayOfWeek; // 0 (Domingo) a 6 (Sábado)
        return in_array($today, $this->trigger_days);
    }
} 