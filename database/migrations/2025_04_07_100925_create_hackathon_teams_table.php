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
        if (!Schema::hasTable('hackathon_teams')) {
            Schema::create('hackathon_teams', function (Blueprint $table) {
                $table->id('team_id');
                // Asegurarnos de usar las claves primarias correctas
                if (Schema::hasColumn('hackathons', 'hackathon_id')) {
                    $table->foreignId('hackathon_id')->constrained('hackathons', 'hackathon_id')->onDelete('cascade');
                } else {
                    $table->foreignId('hackathon_id')->constrained('hackathons', 'id')->onDelete('cascade');
                }
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('project_name')->nullable();
                $table->text('project_description')->nullable();
                $table->timestamps();

                // Índices
                $table->index('hackathon_id');
            });
        }

        if (!Schema::hasTable('hackathon_team_user')) {
            Schema::create('hackathon_team_user', function (Blueprint $table) {
                if (Schema::hasColumn('hackathon_teams', 'team_id')) {
                    $table->foreignId('team_id')->constrained('hackathon_teams', 'team_id')->onDelete('cascade');
                } else {
                    $table->foreignId('team_id')->constrained('hackathon_teams', 'id')->onDelete('cascade');
                }
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                } else {
                    $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');
                }
                
                $table->boolean('is_leader')->default(false);
                $table->timestamps();

                $table->primary(['team_id', 'user_id']);
                $table->index('is_leader');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_team_user');
        Schema::dropIfExists('hackathon_teams');
    }
};
