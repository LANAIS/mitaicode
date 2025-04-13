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
        if (!Schema::hasTable('challenge_student_progress')) {
            Schema::create('challenge_student_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('challenge_id');
                $table->unsignedBigInteger('student_id');
                $table->integer('current_exercise')->default(1);
                $table->integer('total_exercises')->default(1);
                $table->integer('attempts')->default(0);
                $table->integer('score')->default(0);
                $table->decimal('progress_percentage', 5, 2)->default(0);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->boolean('is_completed')->default(false);
                $table->json('exercise_results')->nullable();
                $table->text('feedback')->nullable();
                $table->string('status')->default('in_progress'); // not_started, in_progress, completed, failed
                $table->timestamps();

                // Restricciones de claves foráneas
                $table->foreign('challenge_id')->references('id')->on('teaching_challenges')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                
                // Solo puede haber un registro por desafío y estudiante
                $table->unique(['challenge_id', 'student_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_student_progress');
    }
};
