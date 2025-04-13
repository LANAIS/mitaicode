<?php

namespace App\Listeners;

use App\Events\NewContentCreated;
use App\Models\EmailNotification;
use App\Models\User;
use App\Mail\NotificationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendNewContentNotification implements ShouldQueue
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
    public function handle(NewContentCreated $event): void
    {
        try {
            // Buscar la notificación de nuevo contenido
            $notification = EmailNotification::where('trigger_event', 'new_content')
                ->where('is_active', true)
                ->first();

            if (!$notification) {
                Log::warning('No se encontró una notificación de nuevo contenido activa');
                return;
            }

            // Obtener usuarios activos para notificarles
            $users = User::where('last_login_at', '>=', now()->subDays(30))->get();

            if ($users->isEmpty()) {
                Log::info('No hay usuarios activos para notificar sobre nuevo contenido');
                return;
            }

            Log::info('Enviando notificación de nuevo contenido a ' . $users->count() . ' usuarios activos');

            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            $enviados = 0;
            $errores = 0;

            foreach ($users as $user) {
                // Verificar las preferencias de notificación del usuario
                $preferences = $user->notificationPreferences;
                if ($preferences && !$preferences->receive_emails) {
                    continue;
                }

                // Variables personalizadas para la plantilla
                $customVariables = [
                    'name' => $user->first_name,
                    'content_title' => $event->contentTitle,
                    'content_type' => $event->contentType,
                    'cta_url' => $event->contentUrl,
                    'cta_text' => 'Ver el contenido'
                ];

                try {
                    // Enviar el correo
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

                    $enviados++;
                } catch (\Exception $e) {
                    Log::error("Error al enviar notificación a {$user->email}: " . $e->getMessage());

                    // Registrar el error en la tabla de logs
                    DB::table('email_notification_logs')->insert([
                        'notification_id' => $notification->id,
                        'user_id' => $user->user_id,
                        'email' => $user->email,
                        'sent' => false,
                        'error_message' => $e->getMessage(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $errores++;
                }
            }

            // Actualizar la última fecha de envío
            $notification->last_sent_at = now();
            $notification->save();

            // Confirmar la transacción
            DB::commit();

            Log::info("Notificación de nuevo contenido procesada: {$enviados} enviados, {$errores} errores.");
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();
            Log::error("Error al procesar notificación de nuevo contenido: " . $e->getMessage());
        }
    }
} 