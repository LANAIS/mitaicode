<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'group_id';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'is_active'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el creador del grupo.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con los usuarios que son miembros del grupo.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_memberships', 'group_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Relación con los hackathones asociados al grupo.
     */
    public function hackathons()
    {
        return $this->belongsToMany(Hackathon::class, 'hackathon_groups', 'group_id', 'hackathon_id');
    }

    /**
     * Verificar si un usuario es miembro del grupo.
     */
    public function isMember($userId)
    {
        return $this->members()->where('users.id', $userId)->exists();
    }

    /**
     * Verificar si un usuario es administrador del grupo.
     */
    public function isAdmin($userId)
    {
        return $this->members()
            ->where('users.id', $userId)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    /**
     * Obtener los hackathones activos para este grupo.
     */
    public function getActiveHackathons()
    {
        return $this->hackathons()->where('status', 'active')->get();
    }
} 