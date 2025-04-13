<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\EmailNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDefaultNotifications extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'notifications:create-defaults';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Crear notificaciones por defecto en el sistema';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle()
    {
        $this->info('Creando notificaciones por defecto...');
        
        // Obtener un usuario administrador para asignarlo como creador
        $adminUser = User::where('role', 'admin')->first();
        
        // Si no hay admin, usar el primer usuario
        if (!$adminUser) {
            $adminUser = User::first();
        }
        
        // Si no hay usuarios, salir
        if (!$adminUser) {
            $this->error('No hay usuarios en la base de datos. No se pueden crear notificaciones.');
            return 1;
        }
        
        $this->info('Usando el usuario "' . $adminUser->username . '" como creador de las notificaciones.');
        
        // Verificar si ya existe una notificación de bienvenida
        $welcomeExists = EmailNotification::where('trigger_event', 'welcome')->exists();
        
        if (!$welcomeExists) {
            // Crear notificación de bienvenida
            $welcome = EmailNotification::create([
                'type' => 'welcome',
                'name' => 'Bienvenida a MitaiCode',
                'subject' => '¡Bienvenido/a a MitaiCode, {{name}}!',
                'content' => '<h1>¡Bienvenido/a a MitaiCode, {{name}}!</h1>
                <p>Estamos muy contentos de que te hayas unido a nuestra plataforma. Aquí podrás aprender inteligencia artificial de forma práctica y divertida.</p>
                <p>En MitaiCode encontrarás:</p>
                <ul>
                    <li>Desafíos prácticos para mejorar tus habilidades</li>
                    <li>Una comunidad de estudiantes y profesores</li>
                    <li>Tutoriales y recursos de aprendizaje</li>
                </ul>
                <p>¡Comienza ahora mismo tu viaje en el mundo de la IA!</p>
                <p><a href="{{cta_url}}" class="btn btn-primary">{{cta_text}}</a></p>',
                'trigger_event' => 'welcome',
                'is_active' => true,
                'send_time' => '08:00:00',
                'created_by' => $adminUser->user_id,
                'audience' => 'all',
            ]);
            
            $this->info('Notificación de bienvenida creada correctamente.');
        } else {
            $this->info('La notificación de bienvenida ya existe en el sistema.');
        }
        
        // Verificar si ya existe una notificación de recordatorio
        $reminderExists = EmailNotification::where('trigger_event', 'inactive_user')->exists();
        
        if (!$reminderExists) {
            // Crear notificación de recordatorio
            $reminder = EmailNotification::create([
                'type' => 'reminder',
                'name' => 'Recordatorio de actividad',
                'subject' => '¡Te extrañamos en MitaiCode, {{name}}!',
                'content' => '<h1>¡Te extrañamos, {{name}}!</h1>
                <p>Hace tiempo que no te vemos por MitaiCode. ¿Por qué no vuelves y continúas aprendiendo?</p>
                <p>Tenemos nuevos desafíos esperándote.</p>
                <p>Tu última conexión fue: {{last_login}}</p>
                <p><a href="{{cta_url}}" class="btn btn-primary">{{cta_text}}</a></p>',
                'trigger_event' => 'inactive_user',
                'trigger_days' => [7], // 7 días de inactividad
                'is_active' => true,
                'send_time' => '10:00:00',
                'created_by' => $adminUser->user_id,
                'audience' => 'all',
            ]);
            
            $this->info('Notificación de recordatorio creada correctamente.');
        } else {
            $this->info('La notificación de recordatorio ya existe en el sistema.');
        }
        
        // Verificar si ya existe una notificación de nuevo contenido
        $newContentExists = EmailNotification::where('trigger_event', 'new_content')->exists();
        
        if (!$newContentExists) {
            // Crear notificación de nuevo contenido
            $newContent = EmailNotification::create([
                'type' => 'news',
                'name' => 'Nuevo contenido disponible',
                'subject' => 'Nuevo contenido disponible en MitaiCode, {{name}}',
                'content' => '<h1>¡Nuevo contenido disponible!</h1>
                <p>Hola {{name}},</p>
                <p>Nos complace informarte que hemos agregado nuevo contenido a la plataforma. ¡No te lo pierdas!</p>
                <p><a href="{{cta_url}}" class="btn btn-primary">{{cta_text}}</a></p>',
                'trigger_event' => 'new_content',
                'is_active' => true,
                'send_time' => '12:00:00',
                'created_by' => $adminUser->user_id,
                'audience' => 'all',
            ]);
            
            $this->info('Notificación de nuevo contenido creada correctamente.');
        } else {
            $this->info('La notificación de nuevo contenido ya existe en el sistema.');
        }
        
        $this->info('¡Notificaciones por defecto configuradas correctamente!');
        return 0;
    }
} 