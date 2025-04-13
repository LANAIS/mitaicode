<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoundSubmission extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'round_submissions';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'submission_id';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'team_id',
        'round_id',
        'project_name',
        'description',
        'submission_url',
        'demo_url',
        'file_path',
        'submitted_by',
        'score',
        'feedback'
    ];

    /**
     * Relación con el equipo.
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Relación con la ronda.
     */
    public function round()
    {
        return $this->belongsTo(HackathonRound::class, 'round_id');
    }

    /**
     * Relación con el usuario que envió la entrega.
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Verificar si el envío tiene una calificación.
     */
    public function isScored()
    {
        return $this->score !== null;
    }

    /**
     * Verificar si el envío tiene retroalimentación.
     */
    public function hasFeedback()
    {
        return !empty($this->feedback);
    }

    /**
     * Obtener la URL de descarga del archivo.
     */
    public function getFileUrl()
    {
        if (!$this->file_path) {
            return null;
        }
        
        return asset('storage/' . $this->file_path);
    }
} 