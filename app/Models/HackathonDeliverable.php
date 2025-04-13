<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class HackathonDeliverable extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'hackathon_deliverables';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'round_id',
        'user_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'feedback',
        'score',
        'evaluated_by',
        'evaluated_at'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'decimal:2',
        'file_size' => 'integer',
        'evaluated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener el equipo al que pertenece este entregable.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(HackathonTeam::class, 'team_id', 'team_id');
    }

    /**
     * Obtener la ronda a la que pertenece este entregable.
     */
    public function round(): BelongsTo
    {
        return $this->belongsTo(HackathonRound::class, 'round_id', 'round_id');
    }

    /**
     * Obtener el usuario que subió este entregable.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Obtener el usuario que evaluó este entregable.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by', 'user_id');
    }

    /**
     * Verificar si el entregable ha sido evaluado.
     *
     * @return bool
     */
    public function isEvaluated(): bool
    {
        return !is_null($this->evaluated_at);
    }

    /**
     * Verificar si un usuario puede evaluar este entregable.
     *
     * @param int $userId
     * @return bool
     */
    public function canBeEvaluatedBy(int $userId): bool
    {
        $round = $this->round;
        if (!$round) return false;

        $hackathon = $round->hackathon;
        if (!$hackathon) return false;

        return $hackathon->isJudge($userId);
    }

    /**
     * Obtener el estado del entregable.
     *
     * @return string
     */
    public function getStatus(): string
    {
        if ($this->isEvaluated()) {
            return 'evaluated';
        }
        return 'pending';
    }
}