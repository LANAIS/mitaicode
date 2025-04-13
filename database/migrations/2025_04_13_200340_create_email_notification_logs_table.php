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
        if (!Schema::hasTable('email_notification_logs')) {
            Schema::create('email_notification_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notification_id')->constrained('email_notifications')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                $table->string('email');
                $table->boolean('sent')->default(false);
                $table->text('error_message')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'notification_id']);
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notification_logs');
    }
};
