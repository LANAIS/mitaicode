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
        if (!Schema::hasTable('user_streaks')) {
            Schema::create('user_streaks', function (Blueprint $table) {
                $table->id();
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                } else {
                    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                }
                
                $table->date('last_activity_date');
                $table->integer('current_streak')->default(1);
                $table->integer('longest_streak')->default(1);
                $table->boolean('has_activity_today')->default(true);
                $table->timestamps();
                
                $table->unique('user_id');
            });
        }

        if (!Schema::hasTable('leaderboards')) {
            Schema::create('leaderboards', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('title')->nullable();
                $table->string('type'); // 'weekly', 'monthly', 'all_time', 'challenge', etc.
                $table->unsignedBigInteger('reference_id')->nullable(); // Para leaderboards específicos de desafíos
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['type', 'reference_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('leaderboard_entries')) {
            Schema::create('leaderboard_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('leaderboard_id')->constrained('leaderboards')->onDelete('cascade');
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                } else {
                    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                }
                
                $table->integer('score')->default(0);
                $table->integer('streak')->default(0);
                $table->integer('completed_challenges')->default(0);
                $table->integer('completed_exercises')->default(0);
                $table->integer('ranking_position')->nullable();
                $table->timestamps();

                $table->unique(['leaderboard_id', 'user_id']);
                $table->index(['leaderboard_id', 'score']);
            });
        }

        if (!Schema::hasTable('user_achievements')) {
            Schema::create('user_achievements', function (Blueprint $table) {
                $table->id();
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                } else {
                    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                }
                
                $table->string('achievement_type');
                $table->string('description');
                $table->integer('points')->default(0);
                $table->string('icon')->nullable();
                $table->timestamp('awarded_at');
                $table->timestamps();

                $table->index(['user_id', 'achievement_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('leaderboard_entries');
        Schema::dropIfExists('leaderboards');
        Schema::dropIfExists('user_streaks');
    }
};
