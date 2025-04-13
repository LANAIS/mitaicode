<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamDeliverable extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'team_deliverables';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'round_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'feedback',
        'score',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el equipo al que pertenece el entregable.
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Relación con el usuario que subió el entregable.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con la ronda de hackathon a la que pertenece el entregable.
     */
    public function round()
    {
        return $this->belongsTo(HackathonRound::class, 'round_id');
    }

    /**
     * Verifica si el entregable ha sido evaluado.
     *
     * @return bool
     */
    public function isEvaluated()
    {
        return $this->score !== null;
    }
} 