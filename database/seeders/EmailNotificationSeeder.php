<?php

namespace Database\Seeders;

use App\Models\EmailNotification;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer usuario administrador para asignarlo como creador
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->command->info('No se encontró un usuario administrador. Usando el primer usuario disponible.');
            $admin = User::first();
        }
        
        if (!$admin) {
            $this->command->error('No se pudo encontrar ningún usuario para asignar como creador de notificaciones.');
            return;
        }
        
        $notifications = [
            // Bienvenida
            [
                'name' => 'Bienvenida a nuevos usuarios',
                'type' => 'welcome',
                'subject' => '¡Bienvenido/a a MitaiCode, {{name}}!',
                'content' => '<h2>¡Hola {{name}}!</h2>
                <p>Te damos la bienvenida a MitaiCode, tu plataforma para aprender programación de forma divertida y efectiva.</p>
                <p>Estamos emocionados de que te hayas unido a nuestra comunidad de desarrolladores. Aquí te dejamos algunos consejos para comenzar:</p>
                <ul>
                    <li>Completa tu perfil para personalizar tu experiencia</li>
                    <li>Explora los desafíos disponibles para tu nivel</li>
                    <li>Únete a nuestro foro para conectar con otros estudiantes</li>
                </ul>
                <p>Si tienes alguna pregunta, no dudes en contactarnos respondiendo a este correo.</p>
                <p>¡Feliz codificación!</p>
                <p>El equipo de MitaiCode</p>',
                'trigger_event' => 'welcome',
                'trigger_days' => null,
                'is_active' => true,
                'send_time' => '10:00:00',
                'created_by' => $admin->user_id,
            ],
            
            // Recordatorio de desafío incompleto
            [
                'name' => 'Recordatorio de desafíos pendientes',
                'type' => 'reminder',
                'subject' => '{{name}}, continúa tu desafío en MitaiCode',
                'content' => '<h2>¡Hola {{name}}!</h2>
                <p>Notamos que comenzaste un desafío pero aún no lo has completado.</p>
                <p>Sabemos que a veces la vida se interpone en el camino de la programación, pero queremos animarte a retomar donde lo dejaste.</p>
                <p>Tu desafío <strong>{{challenge_name}}</strong> está esperando por ti. Recuerda que completar desafíos te ayuda a:</p>
                <ul>
                    <li>Mejorar tus habilidades de codificación</li>
                    <li>Acumular puntos XP (actualmente tienes {{xp_points}})</li>
                    <li>Subir de nivel (estás en el nivel {{level}})</li>
                </ul>
                <p>¡Continúa tu camino de aprendizaje hoy!</p>',
                'trigger_event' => 'incomplete_challenge',
                'trigger_days' => [1, 3, 5], // Enviar los lunes, miércoles y viernes
                'is_active' => true,
                'send_time' => '17:00:00',
                'created_by' => $admin->user_id,
            ],
            
            // Reactivación de usuarios inactivos 7 días
            [
                'name' => 'Reactivación - 7 días inactivo',
                'type' => 'inactive',
                'subject' => '{{name}}, te extrañamos en MitaiCode',
                'content' => '<h2>¡Hola {{name}}!</h2>
                <p>Ha pasado una semana desde tu última visita a MitaiCode y queremos saber cómo estás.</p>
                <p>¿Sabías que la consistencia es clave para aprender programación? Incluso dedicando solo 15 minutos al día, puedes mantener tu racha de aprendizaje.</p>
                <p>Tenemos nuevos desafíos y contenido esperando por ti. ¡No pierdas el impulso!</p>
                <p>Vuelve hoy y mantén tu progreso en el nivel {{level}}.</p>',
                'trigger_event' => 'inactive_user',
                'trigger_days' => [7], // 7 días de inactividad
                'is_active' => true,
                'send_time' => '09:00:00',
                'created_by' => $admin->user_id,
            ],
            
            // Reactivación de usuarios inactivos 30 días
            [
                'name' => 'Reactivación - 30 días inactivo',
                'type' => 'inactive',
                'subject' => '{{name}}, ¿todo bien? Te echamos de menos en MitaiCode',
                'content' => '<h2>¡Hola {{name}}!</h2>
                <p>Ha pasado un mes desde tu última visita a MitaiCode y queremos recordarte que tu cuenta sigue activa.</p>
                <p>Durante este tiempo hemos añadido:</p>
                <ul>
                    <li>Nuevos desafíos de programación</li>
                    <li>Mejoras en la plataforma</li>
                    <li>Material de aprendizaje actualizado</li>
                </ul>
                <p>No queremos que pierdas tu progreso ni la oportunidad de seguir mejorando tus habilidades de programación.</p>
                <p>¿Qué tal si retomas tu aprendizaje hoy mismo?</p>',
                'trigger_event' => 'inactive_user',
                'trigger_days' => [30], // 30 días de inactividad
                'is_active' => true,
                'send_time' => '11:00:00',
                'created_by' => $admin->user_id,
            ],
            
            // Nuevo contenido
            [
                'name' => 'Nuevo contenido disponible',
                'type' => 'new_content',
                'subject' => '¡Nuevo contenido disponible en MitaiCode!',
                'content' => '<h2>¡Hola {{name}}!</h2>
                <p>Nos complace anunciarte que hemos publicado nuevo contenido en MitaiCode!</p>
                <p>Como estudiante de nivel {{level}}, te recomendamos especialmente revisar:</p>
                <ul>
                    <li>Nuevos desafíos de algoritmos</li>
                    <li>Material actualizado sobre estructuras de datos</li>
                    <li>Proyectos prácticos para mejorar tu portafolio</li>
                </ul>
                <p>Inicia sesión hoy para descubrir todo el nuevo contenido y seguir progresando en tu camino como desarrollador.</p>',
                'trigger_event' => 'new_content',
                'trigger_days' => null,
                'is_active' => true,
                'send_time' => '12:00:00',
                'created_by' => $admin->user_id,
            ],
            
            // Estancamiento en nivel
            [
                'name' => 'Recordatorio de progreso de nivel',
                'type' => 'reminder',
                'subject' => 'Lleva tu nivel de programación al siguiente nivel, {{name}}',
                'content' => '<h2>¡Hola {{name}}!</h2>
                <p>Hemos notado que has estado en el nivel {{level}} por un tiempo.</p>
                <p>Sabemos que avanzar puede ser desafiante, pero estamos aquí para ayudarte a progresar.</p>
                <p>Aquí hay algunas sugerencias para ayudarte a subir al siguiente nivel:</p>
                <ul>
                    <li>Completa los desafíos pendientes en tu dashboard</li>
                    <li>Revisa el material de aprendizaje para reforzar conceptos</li>
                    <li>Participa en nuestros foros para resolver dudas</li>
                </ul>
                <p>Recuerda que cada pequeño paso te acerca a convertirte en un mejor programador.</p>',
                'trigger_event' => 'level_reminder',
                'trigger_days' => [14], // 14 días en el mismo nivel
                'is_active' => true,
                'send_time' => '18:00:00',
                'created_by' => $admin->user_id,
            ],
            
            // Marketing - Evento especial
            [
                'name' => 'Promoción de hackathon mensual',
                'type' => 'marketing',
                'subject' => '¡Únete al próximo Hackathon de MitaiCode, {{name}}!',
                'content' => '<h2>¡Hola {{name}}!</h2>
                <p>Tenemos el agrado de invitarte a nuestro próximo Hackathon virtual que se celebrará este fin de semana.</p>
                <p><strong>Detalles del evento:</strong></p>
                <ul>
                    <li>🗓️ Fecha: Este fin de semana</li>
                    <li>⏰ Duración: 48 horas</li>
                    <li>🏆 Premios: Cursos premium, mentorías y más</li>
                </ul>
                <p>Es una excelente oportunidad para poner a prueba tus habilidades, trabajar en equipo y crear algo increíble en un corto período.</p>
                <p>Los participantes de todos los niveles son bienvenidos, ¡incluso principiantes!</p>
                <p>¡Inscríbete ahora y prepárate para el desafío!</p>',
                'trigger_event' => 'new_content',
                'trigger_days' => [1, 2, 3, 4, 5], // De lunes a viernes
                'is_active' => true,
                'send_time' => '15:00:00',
                'created_by' => $admin->user_id,
            ],
        ];
        
        foreach ($notifications as $notification) {
            EmailNotification::create($notification);
        }
        
        $this->command->info('Se han creado las notificaciones de email predeterminadas.');
    }
} 