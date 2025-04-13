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
        if (!Schema::hasTable('challenge_user')) {
            Schema::create('challenge_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('is_completed')->default(false);
                $table->integer('progress')->default(0);
                $table->integer('points_earned')->default(0);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->string('status')->default('in_progress'); // not_started, in_progress, completed, abandoned
                $table->timestamps();
                
                $table->unique(['challenge_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_user');
    }
};
