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
        Schema::create('hackathon_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('hackathon_teams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['leader', 'member'])->default('member');
            $table->boolean('invitation_accepted')->default(false);
            $table->timestamps();
            
            // Un usuario solo puede pertenecer una vez a un equipo
            $table->unique(['team_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_team_members');
    }
}; 