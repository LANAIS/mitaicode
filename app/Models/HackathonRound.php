<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HackathonRound extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'hackathon_rounds';

    /**
     * Clave primaria del modelo.
     *
     * @var string
     */
    protected $primaryKey = 'round_id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hackathon_id',
        'name',
        'description',
        'round_number',
        'start_date',
        'end_date',
        'objectives',
        'deliverables',
        'title',
        'duration'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'round_number' => 'integer',
        'duration' => 'integer',
    ];

    /**
     * Obtener el hackathon al que pertenece esta ronda.
     */
    public function hackathon()
    {
        return $this->belongsTo(Hackathon::class, 'hackathon_id', 'id');
    }

    /**
     * Obtener las entregas de los equipos para esta ronda.
     */
    public function teamDeliverables()
    {
        return $this->hasMany(HackathonDeliverable::class, 'round_id', 'round_id');
    }
    
    /**
     * Obtener todos los equipos con entregas en esta ronda.
     */
    public function teamsWithDeliverables()
    {
        return HackathonTeam::whereHas('deliverables', function($query) {
            $query->where('round_id', $this->round_id);
        })->get();
    }
    
    /**
     * Obtener el porcentaje de equipos que han entregado en esta ronda.
     */
    public function submissionPercentage()
    {
        $hackathon = $this->hackathon;
        if (!$hackathon) return 0;
        
        $totalTeams = $hackathon->teams()->count();
        if ($totalTeams === 0) return 0;
        
        $teamsWithSubmissions = $this->teamsWithDeliverables()->count();
        
        return round(($teamsWithSubmissions / $totalTeams) * 100);
    }
    
    /**
     * Obtener el porcentaje de entregas evaluadas en esta ronda.
     */
    public function evaluationPercentage()
    {
        $totalDeliverables = $this->teamDeliverables()->count();
        if ($totalDeliverables === 0) return 0;
        
        $evaluatedDeliverables = $this->teamDeliverables()->whereNotNull('evaluated_at')->count();
        
        return round(($evaluatedDeliverables / $totalDeliverables) * 100);
    }

    /**
     * Verificar si la ronda está activa actualmente basado en el campo is_active.
     *
     * @return bool
     */
    public function isActive()
    {
        $hackathon = $this->hackathon;
        if (!$hackathon) {
            return false;
        }
        
        return $hackathon->current_round == $this->round_id;
    }
    
    /**
     * Verificar si la ronda está actualmente en curso basado en las fechas.
     *
     * @return bool
     */
    public function isCurrentlyActive()
    {
        $now = Carbon::now();
        
        // Si no hay fecha de inicio o fin, consideramos que la ronda es activa
        // si es la ronda actual del hackathon
        if (empty($this->start_date) || empty($this->end_date)) {
            $hackathon = $this->hackathon;
            if ($hackathon) {
                $currentRound = $hackathon->getCurrentRound();
                return $currentRound && $currentRound->round_id == $this->round_id;
            }
            return false;
        }
        
        try {
            return $now->between($this->start_date, $this->end_date);
        } catch (\Exception $e) {
            // Si hay error en la comparación de fechas, consideramos si es la ronda actual
            $hackathon = $this->hackathon;
            if ($hackathon) {
                $currentRound = $hackathon->getCurrentRound();
                return $currentRound && $currentRound->round_id == $this->round_id;
            }
            return false;
        }
    }

    /**
     * Verificar si la ronda ya ha finalizado.
     *
     * @return bool
     */
    public function hasEnded()
    {
        return Carbon::now()->isAfter($this->end_date);
    }
    
    /**
     * Verificar si la ronda aún no ha comenzado.
     *
     * @return bool
     */
    public function hasNotStarted()
    {
        return Carbon::now()->isBefore($this->start_date);
    }
    
    /**
     * Obtiene el estado actual de la ronda (por comenzar, en curso, finalizada).
     *
     * @return string
     */
    public function getCurrentStatus()
    {
        if ($this->hasNotStarted()) {
            return 'pending';
        } elseif ($this->hasEnded()) {
            return 'finished';
        } else {
            return 'active';
        }
    }
    
    /**
     * Verificar si un usuario puede subir entregables a esta ronda.
     *
     * @param int $userId
     * @return bool
     */
    public function canSubmitDeliverables($userId)
    {
        // Verificar si la ronda es la actual del hackathon o está marcada como activa
        $hackathon = $this->hackathon;
        
        if (!$hackathon) {
            return false;
        }
        
        // Verificar si la ronda es la actual o está explícitamente marcada como activa (is_active)
        $currentRound = $hackathon->getCurrentRound();
        $isCurrentRound = $currentRound && $currentRound->round_id == $this->round_id;
        $isActive = $this->is_active;
        
        if (!$isCurrentRound && !$isActive) {
            return false;
        }
        
        // La ronda debe estar dentro de su periodo (entre fechas de inicio y fin)
        // pero solo si ambas fechas están definidas
        if ($this->start_date && $this->end_date) {
            $now = Carbon::now();
            $inDateRange = $now->between($this->start_date, $this->end_date);
            
            if (!$inDateRange) {
                return false;
            }
        }
        
        // El usuario debe pertenecer a un equipo en este hackathon
        return $hackathon->isUserParticipating($userId);
    }

    /**
     * Método de depuración que retorna la razón por la que un usuario no puede subir entregables.
     * Útil para diagnosticar problemas con la validación.
     *
     * @param int $userId
     * @return array
     */
    public function debugCanSubmitDeliverables($userId)
    {
        $hackathon = $this->hackathon;
        $response = [
            'can_submit' => false,
            'reason' => '',
            'details' => []
        ];
        
        if (!$hackathon) {
            $response['reason'] = 'No se encontró el hackathon';
            return $response;
        }
        
        $response['details']['hackathon_id'] = $hackathon->hackathon_id;
        $response['details']['hackathon_current_round'] = $hackathon->current_round;
        $response['details']['this_round_id'] = $this->round_id;
        $response['details']['is_active'] = $this->is_active;

        // Verificar si la ronda es la actual del hackathon o está marcada como activa
        $currentRound = $hackathon->getCurrentRound();
        $response['details']['current_round'] = $currentRound ? $currentRound->round_id : 'Ninguna';
        
        $isCurrentRound = $currentRound && $currentRound->round_id == $this->round_id;
        $isActive = $this->is_active;
        $response['details']['is_current_round'] = $isCurrentRound;
        $response['details']['is_round_active'] = $isActive;

        if (!$isCurrentRound && !$isActive) {
            $response['reason'] = 'Esta no es la ronda actual del hackathon ni está marcada como activa';
            return $response;
        }

        // Verificar fechas
        if ($this->start_date && $this->end_date) {
            $now = Carbon::now();
            $response['details']['now'] = $now->format('Y-m-d H:i:s');
            $response['details']['start_date'] = $this->start_date ? $this->start_date->format('Y-m-d H:i:s') : 'Nula';
            $response['details']['end_date'] = $this->end_date ? $this->end_date->format('Y-m-d H:i:s') : 'Nula';
            
            try {
                $inDateRange = $now->between($this->start_date, $this->end_date);
                $response['details']['in_date_range'] = $inDateRange;
                
                if (!$inDateRange) {
                    $response['reason'] = 'La fecha actual no está dentro del periodo de la ronda';
                    return $response;
                }
            } catch (\Exception $e) {
                $response['reason'] = 'Error al verificar fechas: ' . $e->getMessage();
                return $response;
            }
        }

        // Verificar si el usuario participa en el hackathon
        $isParticipating = $hackathon->isUserParticipating($userId);
        $response['details']['is_user_participating'] = $isParticipating;
        
        if (!$isParticipating) {
            $response['reason'] = 'El usuario no pertenece a ningún equipo en este hackathon';
            return $response;
        }

        // Si pasa todas las validaciones, puede subir entregables
        $response['can_submit'] = true;
        $response['reason'] = 'El usuario puede subir entregables a esta ronda';
        
        return $response;
    }
}