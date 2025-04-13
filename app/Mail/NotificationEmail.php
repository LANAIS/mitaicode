<?php

namespace App\Mail;

use App\Models\User;
use App\Models\EmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NotificationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * El usuario al que se enviará el correo.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * La notificación que se enviará.
     *
     * @var \App\Models\EmailNotification
     */
    public $notification;

    /**
     * Variables personalizadas para la plantilla.
     *
     * @var array
     */
    public $customVariables;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, EmailNotification $notification, array $customVariables = [])
    {
        $this->user = $user;
        $this->notification = $notification;
        $this->customVariables = $customVariables;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->parseTemplate($this->notification->subject),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'content' => $this->parseTemplate($this->notification->content),
                'user' => $this->user,
                'notificationType' => $this->notification->type,
                'customVariables' => $this->customVariables,
            ],
        );
    }

    /**
     * Parsear la plantilla y reemplazar variables
     */
    private function parseTemplate($template)
    {
        $content = $template;
        
        // Reemplazar variables del usuario
        $content = str_replace('{{name}}', $this->user->first_name, $content);
        $content = str_replace('{{email}}', $this->user->email, $content);
        $content = str_replace('{{username}}', $this->user->username, $content);
        
        // Reemplazar variables del perfil de estudiante si existe
        if ($this->user->studentProfile) {
            $content = str_replace('{{level}}', $this->user->studentProfile->level, $content);
            $content = str_replace('{{xp}}', $this->user->studentProfile->xp_points, $content);
        }
        
        // Reemplazar variables personalizadas
        foreach ($this->customVariables as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value, $content);
        }
        
        return $content;
    }
} 