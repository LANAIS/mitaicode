<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\EmailNotification;
use App\Mail\NotificationEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-notifications {--type=all} {--test : Envía solo un correo de prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía las notificaciones programadas por correo electrónico a los usuarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $isTest = $this->option('test');

        if ($isTest) {
            $this->info('Modo de prueba activado - solo se enviará un correo a un usuario de prueba');
        }

        // Obtener las notificaciones activas
        $query = EmailNotification::where('is_active', true);
        
        // Filtrar por tipo si se especifica
        if ($type !== 'all') {
            $query->where('type', $type);
        }
        
        // Verificar la hora de envío (dentro de la ventana de 5 minutos desde la hora programada)
        $now = Carbon::now();
        $timeStart = $now->copy()->subMinutes(5);
        $timeEnd = $now->copy()->addMinutes(5);
        
        $notifications = $query->whereRaw('TIME(send_time) BETWEEN ? AND ?', [
            $timeStart->format('H:i:s'),
            $timeEnd->format('H:i:s')
        ])->get();
        
        if ($notifications->isEmpty()) {
            $this->info('No hay notificaciones programadas para enviar en este momento.');
            return 0;
        }
        
        $this->info('Se encontraron ' . $notifications->count() . ' notificaciones para procesar.');
        
        $totalEnviados = 0;
        
        foreach ($notifications as $notification) {
            if (!$notification->shouldSendToday() && !$isTest) {
                $this->info("Saltando notificación '{$notification->name}' - No programada para hoy.");
                continue;
            }
            
            $this->info("Procesando notificación: {$notification->name}");
            
            // Obtener usuarios calificados para recibir esta notificación
            if ($isTest) {
                // En modo de prueba, usar un usuario administrador (normalmente el primero)
                $users = User::where('role', 'admin')->limit(1)->get();
                if ($users->isEmpty()) {
                    $users = User::limit(1)->get(); // Si no hay admin, usar cualquier usuario
                }
            } else {
                $users = $notification->getQualifiedUsers();
            }
            
            $this->info("Usuarios calificados: " . $users->count());
            
            $enviados = 0;
            $errores = 0;
            
            // Iniciar una transacción de base de datos
            DB::beginTransaction();
            
            try {
                foreach ($users as $user) {
                    // Verificar preferencias de notificación del usuario
                    $preferences = $user->notificationPreferences;
                    
                    if ($preferences && !$preferences->receive_emails) {
                        continue; // Usuario ha desactivado todas las notificaciones
                    }
                    
                    if ($preferences) {
                        $prefField = 'receive_' . $notification->type . '_emails';
                        if (isset($preferences->$prefField) && !$preferences->$prefField) {
                            continue; // Usuario ha desactivado este tipo específico de notificación
                        }
                    }
                    
                    // Evitar envíos duplicados (verificar si ya se envió hoy)
                    $logExists = DB::table('email_notification_logs')
                        ->where('notification_id', $notification->id)
                        ->where('user_id', $user->user_id)
                        ->whereDate('created_at', Carbon::today())
                        ->exists();
                        
                    if ($logExists && !$isTest) {
                        continue;
                    }
                    
                    try {
                        // Crear variables personalizadas para la plantilla según el tipo
                        $customVariables = [];
                        
                        if ($notification->trigger_event === 'inactive_user') {
                            $customVariables['cta_url'] = route('home');
                            $customVariables['cta_text'] = 'Volver a MitaiCode';
                            $customVariables['last_login'] = $user->last_login_at ? $user->last_login_at->format('d/m/Y') : 'Nunca';
                        } elseif ($notification->trigger_event === 'incomplete_challenge') {
                            $challenge = $user->challengeProgress()->where('is_completed', false)->first();
                            if ($challenge) {
                                $customVariables['challenge_name'] = $challenge->challenge_type . ' - ' . $challenge->level;
                                $customVariables['cta_url'] = route('challenges.show', $challenge->id);
                                $customVariables['cta_text'] = 'Continuar Desafío';
                            }
                        }
                        
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
                        
                        if ($isTest) {
                            $this->info("Correo de prueba enviado a: {$user->email}");
                            break; // En modo prueba, enviar solo un correo
                        }
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
                
                $this->info("Notificación '{$notification->name}' procesada: {$enviados} enviados, {$errores} errores.");
                $totalEnviados += $enviados;
                
            } catch (\Exception $e) {
                // Revertir la transacción en caso de error
                DB::rollBack();
                Log::error("Error al procesar notificación {$notification->id}: " . $e->getMessage());
                $this->error("Error al procesar notificación: " . $e->getMessage());
            }
        }
        
        $this->info("Proceso completado. Total de correos enviados: {$totalEnviados}");
        return 0;
    }
} 