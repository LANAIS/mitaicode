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
        if (!Schema::hasTable('hackathon_deliverables')) {
            Schema::create('hackathon_deliverables', function (Blueprint $table) {
                $table->id('deliverable_id');
                
                // Verificar las claves primarias correctas
                if (Schema::hasColumn('hackathon_teams', 'team_id')) {
                    $table->foreignId('team_id')->constrained('hackathon_teams', 'team_id')->onDelete('cascade');
                } else {
                    $table->foreignId('team_id')->constrained('hackathon_teams', 'id')->onDelete('cascade');
                }
                
                if (Schema::hasColumn('hackathon_rounds', 'round_id')) {
                    $table->foreignId('round_id')->constrained('hackathon_rounds', 'round_id')->onDelete('cascade');
                } else {
                    $table->foreignId('round_id')->constrained('hackathon_rounds', 'id')->onDelete('cascade');
                }
                
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('file_path')->nullable();
                $table->string('repository_url')->nullable();
                $table->text('feedback')->nullable();
                $table->decimal('score', 5, 2)->nullable();
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->foreignId('evaluated_by')->nullable()->constrained('users', 'user_id')->onDelete('set null');
                } else {
                    $table->foreignId('evaluated_by')->nullable()->constrained('users', 'id')->onDelete('set null');
                }
                
                $table->timestamp('evaluated_at')->nullable();
                $table->timestamps();

                // Índices
                $table->index('team_id');
                $table->index('round_id');
                $table->index('evaluated_by');
                $table->index('evaluated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_deliverables');
    }
}; 