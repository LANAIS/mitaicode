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
        // Categorías para las lecciones
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->timestamps();
        });

        // Lecciones de prompt engineering
        Schema::create('prompt_lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('description');
            $table->json('content');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->boolean('is_public')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('estimated_time')->nullable(); // en minutos
            $table->unsignedBigInteger('category_id')->nullable();
            $table->integer('points')->default(0);
            $table->timestamps();

            $table->foreign('teacher_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        // Ejercicios dentro de las lecciones
        Schema::create('prompt_exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->text('instructions');
            $table->text('example_prompt')->nullable();
            $table->text('hint')->nullable();
            $table->json('evaluation_criteria')->nullable();
            $table->integer('order')->default(0);
            $table->integer('points')->default(0);
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('prompt_lessons')->onDelete('cascade');
        });

        // Progreso de estudiantes en lecciones
        Schema::create('prompt_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('student_id');
            $table->integer('completed_exercises')->default(0);
            $table->integer('total_exercises')->default(0);
            $table->integer('score')->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('prompt_lessons')->onDelete('cascade');
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->unique(['lesson_id', 'student_id']);
        });

        // Resoluciones de ejercicios
        Schema::create('prompt_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('student_id');
            $table->text('prompt_text');
            $table->text('ai_response')->nullable();
            $table->integer('score')->default(0);
            $table->text('feedback')->nullable();
            $table->enum('status', ['submitted', 'graded', 'rejected'])->default('submitted');
            $table->integer('attempt_number')->default(1);
            $table->timestamps();

            $table->foreign('exercise_id')->references('id')->on('prompt_exercises')->onDelete('cascade');
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
        });

        // Entregables de estudiantes
        Schema::create('prompt_deliverables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('student_id');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->text('content')->nullable();
            $table->float('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['draft', 'submitted', 'graded', 'returned'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('prompt_lessons')->onDelete('cascade');
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_deliverables');
        Schema::dropIfExists('prompt_submissions');
        Schema::dropIfExists('prompt_lesson_progress');
        Schema::dropIfExists('prompt_exercises');
        Schema::dropIfExists('prompt_lessons');
        Schema::dropIfExists('categories');
    }
};
