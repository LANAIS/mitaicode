<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'receive_emails',
        'receive_welcome_emails',
        'receive_reminder_emails',
        'receive_inactive_emails',
        'receive_new_content_emails',
        'receive_marketing_emails',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'receive_emails' => 'boolean',
        'receive_welcome_emails' => 'boolean',
        'receive_reminder_emails' => 'boolean',
        'receive_inactive_emails' => 'boolean',
        'receive_new_content_emails' => 'boolean',
        'receive_marketing_emails' => 'boolean',
    ];

    /**
     * Obtener el usuario al que pertenecen estas preferencias.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
} 