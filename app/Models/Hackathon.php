<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Hackathon extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'hackathons';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'created_by',
        'max_participants',
        'max_teams',
        'team_size',
        'current_round',
        'start_date',
        'end_date',
        'status'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'team_size' => 'integer',
        'max_teams' => 'integer',
        'max_participants' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para obtener hackathones activos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'asc');
    }

    /**
     * Scope para obtener hackathones pasados.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePast(Builder $query)
    {
        return $query->where('status', 'finished')
            ->orWhere('end_date', '<', now())
            ->orderBy('end_date', 'desc');
    }

    /**
     * Scope para obtener hackathones futuros.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming(Builder $query)
    {
        return $query->where('status', 'pending')
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc');
    }

    /**
     * Relación con el usuario que creó el hackathon.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Relación con los equipos que participan en el hackathon.
     */
    public function teams()
    {
        return $this->hasMany(HackathonTeam::class, 'hackathon_id', 'id');
    }

    /**
     * Relación con las rondas del hackathon.
     */
    public function rounds()
    {
        return $this->hasMany(HackathonRound::class, 'hackathon_id', 'id');
    }

    /**
     * Relación con los grupos que tienen acceso al hackathon.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'hackathon_groups', 'hackathon_id', 'group_id');
    }
    
    /**
     * Relación con los jurados del hackathon.
     */
    public function judges()
    {
        return $this->belongsToMany(User::class, 'hackathon_judges', 'hackathon_id', 'user_id')
                    ->withPivot('is_lead_judge', 'notes')
                    ->withTimestamps();
    }
    
    /**
     * Verificar si un usuario es jurado en este hackathon.
     *
     * @param int $userId
     * @return bool
     */
    public function isJudge($userId)
    {
        return $this->judges()->where('hackathon_judges.user_id', $userId)->exists();
    }
    
    /**
     * Obtener el número de entregables para este hackathon.
     *
     * @return int
     */
    public function deliverablesCount()
    {
        $count = 0;
        foreach ($this->rounds as $round) {
            $count += $round->deliverables()->count();
        }
        return $count;
    }
    
    /**
     * Obtener el número de entregables pendientes de evaluación.
     *
     * @return int
     */
    public function pendingDeliverablesCount()
    {
        $count = 0;
        foreach ($this->rounds as $round) {
            $count += $round->deliverables()->whereNull('evaluated_at')->count();
        }
        return $count;
    }

    /**
     * Verificar si un usuario puede participar en este hackathon.
     */
    public function canUserParticipate($userId)
    {
        // Verificar si el hackathon está activo
        if ($this->status !== 'active') {
            return false;
        }

        // Temporalmente permitir a todos los usuarios participar
        return true;
        
        // Comentamos la validación de grupo original
        // return $this->groups()
        //     ->whereHas('members', function ($query) use ($userId) {
        //         $query->where('user_id', $userId);
        //     })
        //     ->exists();
    }

    /**
     * Verificar si un usuario ya está participando en este hackathon.
     */
    public function isUserParticipating($userId)
    {
        return $this->teams()
                    ->whereHas('members', function ($query) use ($userId) {
                        $query->where('hackathon_team_user.user_id', $userId);
                    })
                    ->exists();
    }

    /**
     * Obtener el equipo de un usuario en este hackathon.
     */
    public function getUserTeam($userId)
    {
        return $this->teams()
                    ->whereHas('members', function ($query) use ($userId) {
                        $query->where('hackathon_team_user.user_id', $userId);
                    })
                    ->first();
    }

    /**
     * @deprecated Use getCurrentRound() instead
     * Este método está obsoleto y puede causar errores si la columna is_active no existe.
     */
    public function currentRound()
    {
        return $this->getCurrentRound();
    }

    /**
     * Obtener la ronda actual del hackathon basado en el campo current_round.
     * Este método es más confiable que currentRound() ya que usa el campo current_round
     * en lugar de is_active que no existe en la tabla.
     */
    public function getCurrentRound()
    {
        // 1. Si tenemos current_round definido, usamos ese valor
        if ($this->current_round) {
            $round = $this->rounds()
                    ->where('round_id', $this->current_round)
                    ->first();
                    
            if ($round) {
                return $round;
            }
        }
        
        // 2. Si no tenemos current_round, o el round_id no existe,
        // buscar rondas activas por fecha (entre start_date y end_date)
        $now = now();
        $roundByDate = $this->rounds()
                ->whereNotNull('start_date')
                ->whereNotNull('end_date')
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->first();
                
        if ($roundByDate) {
            return $roundByDate;
        }
        
        // 3. Si no encontramos rondas activas por fecha, devolvemos la primera ronda
        return $this->rounds()->first();
    }

    /**
     * Verificar si el hackathon está activo.
     */
    public function isActive()
    {
        return $this->status === 'active' &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }

    /**
     * Verificar si el hackathon ha terminado.
     */
    public function hasEnded()
    {
        return $this->status === 'finished' ||
               $this->end_date < now();
    }
} 