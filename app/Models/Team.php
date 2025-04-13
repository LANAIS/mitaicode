<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'teams';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'team_id';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'hackathon_id',
        'team_name',
        'description',
        'leader_id',
        'repository_url'
    ];

    /**
     * Relación con el hackathon al que pertenece el equipo.
     */
    public function hackathon()
    {
        return $this->belongsTo(Hackathon::class, 'hackathon_id');
    }

    /**
     * Relación con el usuario líder del equipo.
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Relación con los miembros del equipo.
     */
    public function members()
    {
        return $this->hasMany(TeamMember::class, 'team_id');
    }

    /**
     * Relación con los usuarios miembros del equipo.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'team_members', 'team_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Relación con los envíos de rondas.
     */
    public function submissions()
    {
        return $this->hasMany(RoundSubmission::class, 'team_id');
    }

    /**
     * Verificar si el equipo está completo (alcanzó el máximo de miembros).
     */
    public function isFull()
    {
        $maxMembers = $this->hackathon->max_team_size ?? 4;
        return $this->members()->count() >= $maxMembers;
    }

    /**
     * Verificar si un usuario es miembro del equipo.
     */
    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Verificar si un usuario es el líder del equipo.
     */
    public function isLeader($userId)
    {
        return $this->leader_id == $userId;
    }

    /**
     * Relación con los mensajes del chat del equipo.
     */
    public function messages()
    {
        return $this->hasMany(TeamMessage::class, 'team_id');
    }

    /**
     * Relación con los entregables del equipo.
     */
    public function deliverables()
    {
        return $this->hasMany(TeamDeliverable::class, 'team_id');
    }
} 