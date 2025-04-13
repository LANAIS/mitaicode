<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class EmailNotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener un usuario administrador para asignarlo como creador
        $adminUser = User::where('role', 'admin')->first();
        
        // Si no hay admin, usar el primer usuario
        if (!$adminUser) {
            $adminUser = User::first();
        }
        
        // Si no hay usuarios, salir
        if (!$adminUser) {
            $this->command->info('No hay usuarios en la base de datos. No se pueden crear notificaciones.');
            return;
        }
        
        // Crear notificación de bienvenida
        DB::table('email_notifications')->insert([
            'type' => 'welcome',
            'name' => 'Bienvenida a MitaiCode',
            'subject' => '¡Bienvenido/a a MitaiCode!',
            'content' => '<h1>¡Bienvenido/a a MitaiCode!</h1><p>Estamos muy contentos de que te hayas unido a nuestra plataforma. Aquí podrás aprender inteligencia artificial de forma práctica y divertida.</p><p>¡Comienza ahora mismo tu viaje en el mundo de la IA!</p>',
            'trigger_event' => 'new_user',
            'is_active' => true,
            'send_time' => '08:00:00',
            'created_by' => $adminUser->user_id,
            'audience' => 'all',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Crear notificación de recordatorio
        DB::table('email_notifications')->insert([
            'type' => 'reminder',
            'name' => 'Recordatorio de actividad',
            'subject' => '¡Te extrañamos en MitaiCode!',
            'content' => '<h1>¡Te extrañamos!</h1><p>Hace tiempo que no te vemos por MitaiCode. ¿Por qué no vuelves y continúas aprendiendo?</p><p>Tenemos nuevos desafíos esperándote.</p>',
            'trigger_event' => 'inactive_user',
            'is_active' => true,
            'send_time' => '10:00:00',
            'created_by' => $adminUser->user_id,
            'audience' => 'all',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Crear notificación de prueba (para ejecutar el comando --test)
        DB::table('email_notifications')->insert([
            'type' => 'test',
            'name' => 'Notificación de prueba',
            'subject' => 'Prueba de envío de notificaciones',
            'content' => '<h1>¡Esta es una prueba!</h1><p>Si recibes este correo, el sistema de notificaciones está funcionando correctamente.</p>',
            'trigger_event' => 'manual',
            'is_active' => true,
            'send_time' => date('H:i:s'), // Hora actual para que se envíe inmediatamente al ejecutar el comando
            'created_by' => $adminUser->user_id,
            'audience' => 'all',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->command->info('Se han creado 3 notificaciones de ejemplo correctamente.');
    }
}
