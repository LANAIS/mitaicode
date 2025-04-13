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
        if (!Schema::hasTable('email_notifications')) {
            Schema::create('email_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('subject');
                $table->text('content');
                $table->string('trigger_event')->nullable();
                $table->string('recipient_type'); // all, admin, student, teacher
                $table->boolean('is_active')->default(true);
                $table->string('template')->default('default');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notifications');
    }
};
