<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\EmailNotification;
use App\Mail\NotificationEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestNotification extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'notifications:test {email?} {--type=welcome : Tipo de notificación (welcome, reminder, new_content)}';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Enviar una notificación de prueba a un correo electrónico';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');
        
        if (!$email) {
            // Si no se proporciona un email, usar el del primer usuario admin
            $user = User::where('role', 'admin')->first();
            if (!$user) {
                $user = User::first();
            }
            
            if (!$user) {
                $this->error('No hay usuarios en el sistema para enviar la notificación de prueba');
                return 1;
            }
            
            $email = $user->email;
            $this->info("Usando el email del usuario: {$email}");
        } else {
            // Verificar si el email proporcionado corresponde a un usuario existente
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("No se encontró ningún usuario con el email {$email}");
                return 1;
            }
        }
        
        // Buscar la notificación según el tipo
        $notification = EmailNotification::where('trigger_event', $type)
            ->where('is_active', true)
            ->first();
        
        if (!$notification) {
            $this->error("No se encontró una notificación activa de tipo {$type}");
            return 1;
        }
        
        $this->info("Enviando notificación de prueba ({$type}) al email {$email}...");
        
        try {
            // Variables personalizadas para la plantilla
            $customVariables = [
                'name' => $user->first_name,
                'cta_url' => route('dashboard'),
                'cta_text' => 'Ir al Dashboard',
                'content_title' => 'Ejemplo de contenido',
                'content_type' => 'Desafío',
                'last_login' => $user->last_login_at ? $user->last_login_at->format('d/m/Y') : 'Nunca'
            ];
            
            // Enviar el correo
            Mail::to($email)->send(new NotificationEmail($user, $notification, $customVariables));
            
            $this->info("¡Notificación de prueba enviada con éxito!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error al enviar la notificación: " . $e->getMessage());
            Log::error("Error al enviar notificación de prueba: " . $e->getMessage());
            return 1;
        }
    }
} 