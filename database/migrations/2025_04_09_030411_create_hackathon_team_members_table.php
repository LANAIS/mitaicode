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
        if (!Schema::hasTable('hackathon_team_members')) {
            Schema::create('hackathon_team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained('hackathon_teams')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->boolean('is_leader')->default(false);
                $table->timestamps();
                
                $table->unique(['team_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_team_members');
    }
};
