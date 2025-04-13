<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HackathonMessage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'hackathon_messages';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'message_id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'content',
        'type',
        'file_path',
        'file_name',
        'file_type',
        'file_size'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Obtener el equipo al que pertenece este mensaje.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(HackathonTeam::class, 'team_id', 'team_id');
    }

    /**
     * Obtener el usuario que envió el mensaje.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Verificar si el mensaje contiene un archivo.
     *
     * @return bool
     */
    public function hasFile(): bool
    {
        return !is_null($this->file_path);
    }

    /**
     * Obtener la URL del archivo adjunto.
     *
     * @return string|null
     */
    public function getFileUrl(): ?string
    {
        if (!$this->hasFile()) {
            return null;
        }

        return asset('storage/' . $this->file_path);
    }

    /**
     * Verificar si un usuario puede ver este mensaje.
     *
     * @param int $userId
     * @return bool
     */
    public function canBeViewedBy(int $userId): bool
    {
        return $this->team->isMember($userId) || 
               $this->team->hackathon->isJudge($userId);
    }
} 