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
        if (!Schema::hasTable('leaderboards')) {
            Schema::create('leaderboards', function (Blueprint $table) {
                $table->id();
                $table->string('type'); // 'weekly', 'monthly', 'all-time', 'hackathon', etc.
                $table->string('name')->nullable();
                $table->string('reference_id')->nullable(); // Para leaderboards específicos (ej. ID de hackathon)
                $table->boolean('is_active')->default(true);
                $table->datetime('start_date')->nullable();
                $table->datetime('end_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('leaderboard_entries')) {
            Schema::create('leaderboard_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('leaderboard_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('points')->default(0);
                $table->integer('position')->nullable();
                $table->json('metadata')->nullable(); // Datos adicionales específicos del leaderboard
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboard_entries');
        Schema::dropIfExists('leaderboards');
    }
};
