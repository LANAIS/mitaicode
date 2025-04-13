<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'team_members';

    /**
     * Indica si el modelo debe tener marcas de tiempo.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'is_leader',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_leader' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el equipo al que pertenece este miembro.
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'team_id');
    }

    /**
     * Obtener el usuario asociado a este miembro del equipo.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Verificar si el miembro es líder del equipo.
     */
    public function isLeader()
    {
        return $this->is_leader;
    }
} 