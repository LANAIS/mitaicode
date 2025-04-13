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
        // Verificar si ya existe la tabla email_notifications
        if (!Schema::hasTable('email_notifications')) {
            Schema::create('email_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('type'); // welcome, reminder, inactive, new_content, etc.
                $table->string('name'); // Nombre descriptivo de la notificación
                $table->string('subject'); // Asunto del correo
                $table->text('content'); // Contenido HTML del correo
                $table->string('trigger_event')->default('manual'); // Evento que dispara la notificación
                $table->json('trigger_days')->nullable(); // Días específicos o parámetros adicionales en JSON
                $table->boolean('is_active')->default(true);
                $table->time('send_time')->default('08:00:00'); // Hora a la que se enviará
                $table->timestamp('last_sent_at')->nullable(); // Última vez que se envió
                $table->unsignedBigInteger('created_by');
                $table->foreign('created_by')->references('user_id')->on('users');
                $table->string('audience')->default('all'); // all, students, teachers
                $table->boolean('show_once')->default(false); // Si la notificación debe mostrarse solo una vez
                $table->timestamp('expires_at')->nullable(); // Fecha de expiración
                $table->timestamps();
            });
        }

        // Verificar si ya existe la tabla email_notification_logs
        if (!Schema::hasTable('email_notification_logs')) {
            Schema::create('email_notification_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notification_id')->constrained('email_notifications')->onDelete('cascade');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->string('email');
                $table->boolean('sent')->default(false);
                $table->boolean('delivered')->default(false);
                $table->boolean('opened')->default(false);
                $table->boolean('clicked')->default(false);
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }

        // Verificar si ya existe la tabla user_notification_preferences
        if (!Schema::hasTable('user_notification_preferences')) {
            Schema::create('user_notification_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->boolean('receive_emails')->default(true);
                $table->boolean('receive_welcome_emails')->default(true);
                $table->boolean('receive_reminder_emails')->default(true);
                $table->boolean('receive_inactive_emails')->default(true);
                $table->boolean('receive_new_content_emails')->default(true);
                $table->boolean('receive_marketing_emails')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No eliminamos nada en down para evitar pérdida de datos accidental
        // Si se necesita eliminar tablas, hacerlo manualmente
    }
};
