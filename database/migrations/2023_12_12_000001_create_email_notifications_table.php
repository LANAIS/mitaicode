<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // welcome, reminder, inactive, new_content, etc.
            $table->string('name'); // Nombre descriptivo de la notificación
            $table->string('subject'); // Asunto del correo
            $table->text('content'); // Contenido HTML del correo
            $table->string('trigger_event'); // Evento que dispara la notificación: inactive_user, incomplete_challenge, etc.
            $table->json('trigger_days')->nullable(); // Días específicos o parámetros adicionales en JSON
            $table->boolean('is_active')->default(true);
            $table->time('send_time')->default('08:00:00'); // Hora a la que se enviará
            $table->timestamp('last_sent_at')->nullable(); // Última vez que se envió
            $table->foreignId('created_by')->constrained('users', 'user_id'); // Administrador que creó la notificación
            $table->timestamps();
        });

        // Tabla relacionada para el seguimiento de los correos enviados
        Schema::create('email_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('email_notifications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('email');
            $table->boolean('sent')->default(false);
            $table->boolean('delivered')->default(false);
            $table->boolean('opened')->default(false);
            $table->boolean('clicked')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // Tabla de configuración de notificaciones por usuario
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->boolean('receive_emails')->default(true);
            $table->boolean('receive_welcome_emails')->default(true);
            $table->boolean('receive_reminder_emails')->default(true);
            $table->boolean('receive_inactive_emails')->default(true);
            $table->boolean('receive_new_content_emails')->default(true);
            $table->boolean('receive_marketing_emails')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
        Schema::dropIfExists('email_notification_logs');
        Schema::dropIfExists('email_notifications');
    }
}; 