<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'user_id';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'role',
        'avatar_url',
        'is_active',
        'last_login_at'
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtener los atributos que deben convertirse.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'date_registered' => 'datetime',
            'last_login' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Obtener el perfil de estudiante asociado con el usuario.
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class, 'user_id', 'user_id');
    }

    /**
     * Obtener el perfil de profesor asociado con el usuario.
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class, 'user_id', 'user_id');
    }

    /**
     * Obtener las clases creadas por el profesor.
     */
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'teacher_id', 'user_id');
    }

    /**
     * Obtener los proyectos del usuario.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'user_id', 'user_id');
    }

    /**
     * Obtener las insignias del usuario.
     */
    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class, 'user_id', 'user_id');
    }

    /**
     * Obtener las clases en las que está inscrito el estudiante.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class, 'student_id', 'user_id');
    }

    /**
     * Obtener el progreso de misiones del usuario.
     */
    public function missionProgress(): HasMany
    {
        return $this->hasMany(MissionProgress::class, 'user_id', 'user_id');
    }

    /**
     * Comprueba si el usuario tiene rol de administrador.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Relación con los grupos del usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id')
            ->withTimestamps();
    }

    /**
     * Obtener el avatar del usuario.
     */
    public function avatar(): HasOne
    {
        return $this->hasOne(UserAvatar::class, 'user_id', 'user_id');
    }

    /**
     * Obtener los items en el inventario del usuario.
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(UserInventory::class, 'user_id', 'user_id');
    }

    /**
     * Obtener los items equipados por el usuario.
     */
    public function equippedItems()
    {
        return $this->inventory()->where('is_equipped', true)
            ->whereNull('expires_at')
            ->orWhere('expires_at', '>', now())
            ->with('item');
    }

    /**
     * Comprar un item de la tienda.
     *
     * @param StoreItem $item
     * @return bool|UserInventory
     */
    public function purchaseItem(StoreItem $item)
    {
        // Verificar si el usuario puede comprar el item
        if (!$item->canBePurchasedBy($this)) {
            return false;
        }
        
        // Reducir los puntos XP del usuario
        if ($this->studentProfile) {
            $this->studentProfile->xp_points -= $item->price;
            $this->studentProfile->save();
        } else {
            return false;
        }
        
        // Reducir el stock si es limitado
        if ($item->is_limited) {
            $item->stock -= 1;
            $item->save();
        }
        
        // Agregar el item al inventario del usuario
        return UserInventory::create([
            'user_id' => $this->user_id,
            'item_id' => $item->item_id,
            'is_equipped' => false,
            'is_used' => false,
            'acquired_at' => now(),
            'expires_at' => $item->duration ? now()->addDays($item->duration) : null,
        ]);
    }

    /**
     * Obtener las preferencias de notificación del usuario.
     */
    public function notificationPreferences(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class, 'user_id', 'user_id');
    }

    /**
     * Obtener el progreso de los desafíos del usuario.
     */
    public function challengeProgress(): HasMany
    {
        return $this->hasMany(ChallengeProgress::class, 'user_id', 'user_id');
    }
}
