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
        Schema::create('exercise_completion_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('submission_id');
            $table->timestamp('completed_at');
            $table->timestamps();
            
            // Índices y claves foráneas
            $table->index('student_id');
            $table->index('exercise_id');
            $table->unique(['student_id', 'exercise_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_completion_records');
    }
};
