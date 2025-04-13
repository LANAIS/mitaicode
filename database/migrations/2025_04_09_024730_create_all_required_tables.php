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
        // Tablas de hackathons
        if (!Schema::hasTable('hackathon_teams')) {
            Schema::create('hackathon_teams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_winner')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hackathons')) {
            Schema::create('hackathons', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->date('start_date');
                $table->date('end_date');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hackathon_team_members')) {
            Schema::create('hackathon_team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained('hackathon_teams')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->boolean('is_leader')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hackathon_rounds')) {
            Schema::create('hackathon_rounds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hackathon_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->datetime('start_date');
                $table->datetime('end_date');
                $table->integer('order')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hackathon_judges')) {
            Schema::create('hackathon_judges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hackathon_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hackathon_deliverables')) {
            Schema::create('hackathon_deliverables', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained('hackathon_teams')->onDelete('cascade');
                $table->foreignId('round_id')->constrained('hackathon_rounds')->onDelete('cascade');
                $table->string('file_path')->nullable();
                $table->text('content')->nullable();
                $table->text('feedback')->nullable();
                $table->float('score')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hackathon_messages')) {
            Schema::create('hackathon_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained('hackathon_teams')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->text('message');
                $table->timestamps();
            });
        }

        // Tablas de desafíos
        if (!Schema::hasTable('challenges')) {
            Schema::create('challenges', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->string('difficulty');
                $table->string('category');
                $table->string('language');
                $table->integer('points')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('challenge_exercises')) {
            Schema::create('challenge_exercises', function (Blueprint $table) {
                $table->id();
                $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('instructions');
                $table->text('initial_code')->nullable();
                $table->text('expected_output')->nullable();
                $table->integer('order')->default(1);
                $table->integer('points')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('challenge_submissions')) {
            Schema::create('challenge_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
                $table->foreignId('exercise_id')->constrained('challenge_exercises')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->text('code_submitted');
                $table->boolean('is_correct')->default(false);
                $table->text('feedback')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('challenge_user')) {
            Schema::create('challenge_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->boolean('is_completed')->default(false);
                $table->integer('score')->default(0);
                $table->string('status')->default('not_started');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('challenge_progress')) {
            Schema::create('challenge_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
                $table->integer('progress_percentage')->default(0);
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        // Tablas de usuarios
        if (!Schema::hasTable('student_profiles')) {
            Schema::create('student_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->string('level')->default('beginner');
                $table->integer('xp_points')->default(0);
                $table->integer('completed_challenges')->default(0);
                $table->string('avatar')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('teacher_profiles')) {
            Schema::create('teacher_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->string('institution')->nullable();
                $table->string('position')->nullable();
                $table->text('bio')->nullable();
                $table->string('website', 255)->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
            });
        }

        // Tablas de gamificación
        if (!Schema::hasTable('badges')) {
            Schema::create('badges', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icon');
                $table->text('description');
                $table->string('category');
                $table->integer('required_points')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->foreignId('badge_id')->constrained()->onDelete('cascade');
                $table->timestamp('awarded_at')->useCurrent();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_streaks')) {
            Schema::create('user_streaks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->integer('current_streak')->default(0);
                $table->integer('longest_streak')->default(0);
                $table->date('last_activity_date')->nullable();
                $table->timestamps();
            });
        }

        // Tablas de tienda virtual
        if (!Schema::hasTable('store_items')) {
            Schema::create('store_items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description');
                $table->string('type'); // avatar, badge, theme, etc.
                $table->integer('price');
                $table->string('image_path')->nullable();
                $table->boolean('is_available')->default(true);
                $table->integer('purchases')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_inventories')) {
            Schema::create('user_inventories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->foreignId('item_id')->constrained('store_items')->onDelete('cascade');
                $table->boolean('is_equipped')->default(false);
                $table->timestamp('purchased_at')->useCurrent();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_avatars')) {
            Schema::create('user_avatars', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->string('avatar_path');
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }

        // Tablas para notificaciones
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
                $table->foreignId('created_by')->constrained('users', 'user_id')->onDelete('cascade');
                $table->string('audience')->default('all'); // all, students, teachers
                $table->boolean('show_once')->default(false); // Si la notificación debe mostrarse solo una vez
                $table->timestamp('expires_at')->nullable(); // Fecha de expiración
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('email_notification_logs')) {
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
        }

        if (!Schema::hasTable('user_notification_preferences')) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No eliminamos nada en down para evitar pérdida de datos accidental
    }
};
