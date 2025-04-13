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
        Schema::create('team_deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams', 'team_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('round_id')->constrained('hackathon_rounds')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->text('feedback')->nullable();
            $table->integer('score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_deliverables');
    }
}; 