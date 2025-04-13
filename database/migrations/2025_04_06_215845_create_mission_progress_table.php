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
        Schema::create('mission_progress', function (Blueprint $table) {
            $table->id('progress_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('mission_id');
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->integer('blocks_used')->default(0);
            $table->integer('attempts')->default(0);
            $table->integer('completion_time')->nullable()->comment('Time in seconds');
            $table->timestamp('completed_at')->nullable();
            $table->text('solution_blocks')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('mission_id')->references('mission_id')->on('missions')->onDelete('cascade');
            $table->unique(['user_id', 'mission_id'], 'unique_user_mission');
            $table->index(['user_id', 'status'], 'idx_user_progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_progress');
    }
};
