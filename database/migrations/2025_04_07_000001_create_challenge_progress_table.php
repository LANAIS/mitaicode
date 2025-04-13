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
        if (!Schema::hasTable('challenge_progress')) {
            Schema::create('challenge_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('challenge_type', 50); // 'python', 'blocks', 'ai_prompt'
                $table->string('level', 20); // 'principiante', 'intermedio', 'avanzado'
                $table->integer('challenge_number');
                $table->text('submitted_code')->nullable();
                $table->boolean('is_completed')->default(false);
                $table->integer('attempts')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
                $table->index('challenge_type');
                $table->index('level');
                $table->index('challenge_number');
            });
        }
        
        if (!Schema::hasTable('user_certificates')) {
            Schema::create('user_certificates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('certificate_type');
                $table->string('level');
                $table->string('certificate_url')->nullable();
                $table->timestamp('awarded_at');
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
        
        if (!Schema::hasTable('user_achievements')) {
            Schema::create('user_achievements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('achievement_type');
                $table->string('description');
                $table->integer('points')->default(0);
                $table->string('icon')->nullable();
                $table->boolean('is_displayed')->default(true);
                $table->timestamp('awarded_at');
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('user_certificates');
        Schema::dropIfExists('challenge_progress');
    }
}; 