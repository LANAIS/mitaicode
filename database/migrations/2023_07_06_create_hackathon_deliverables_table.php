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
        Schema::create('hackathon_deliverables', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->foreignId('team_id')->constrained('hackathon_teams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('round_id')->constrained('hackathon_rounds')->onDelete('cascade');
            $table->decimal('score', 4, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Índices
            $table->index('team_id');
            $table->index('user_id');
            $table->index('round_id');
            $table->index('evaluated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_deliverables');
    }
};