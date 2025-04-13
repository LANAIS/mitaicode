<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\EmailNotification;
use App\Mail\NotificationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        try {
            // Buscar la notificación de bienvenida
            $notification = EmailNotification::where('trigger_event', 'welcome')
                ->where('is_active', true)
                ->first();

            if (!$notification) {
                Log::warning('No se encontró una notificación de bienvenida activa');
                return;
            }

            $user = $event->user;

            // Verificar las preferencias de notificación del usuario si existen
            $preferences = $user->notificationPreferences;
            if ($preferences && !$preferences->receive_emails) {
                return;
            }

            // Variables personalizadas para la plantilla de bienvenida
            $customVariables = [
                'name' => $user->first_name,
                'cta_url' => route('dashboard'),
                'cta_text' => 'Comenzar a Aprender'
            ];

            // Enviar el correo de bienvenida
            Mail::to($user->email)->queue(new NotificationEmail($user, $notification, $customVariables));

            // Registrar el envío en la tabla de logs
            DB::table('email_notification_logs')->insert([
                'notification_id' => $notification->id,
                'user_id' => $user->user_id,
                'email' => $user->email,
                'sent' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Actualizar la última fecha de envío
            $notification->last_sent_at = now();
            $notification->save();

            Log::info("Correo de bienvenida enviado a: {$user->email}");
        } catch (\Exception $e) {
            Log::error("Error al enviar correo de bienvenida: " . $e->getMessage());
        }
    }
} 