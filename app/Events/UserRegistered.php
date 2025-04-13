<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * El usuario que se ha registrado.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Crear una nueva instancia del evento.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
} 