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
        // Tabla principal de desafíos
        if (!Schema::hasTable('teaching_challenges')) {
            Schema::create('teaching_challenges', function (Blueprint $table) {
                $table->id();
                $table->string('title', 150);
                $table->text('description');
                $table->text('objectives');
                $table->text('instructions');
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->unsignedBigInteger('teacher_id');
                    $table->foreign('teacher_id')->references('user_id')->on('users')->onDelete('cascade');
                } else {
                    $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                }
                
                if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'class_id')) {
                    $table->unsignedBigInteger('class_id')->nullable(); // Puede ser asignado a una clase específica o ser público
                    $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('set null');
                } else if (Schema::hasTable('classrooms')) {
                    $table->foreignId('class_id')->nullable()->constrained('classrooms')->onDelete('set null');
                } else {
                    $table->unsignedBigInteger('class_id')->nullable();
                }
                
                $table->boolean('is_public')->default(false);
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->enum('challenge_type', ['python', 'ai_prompt']); // Tipo de desafío
                $table->enum('difficulty', ['principiante', 'intermedio', 'avanzado'])->default('principiante');
                $table->integer('estimated_time')->nullable(); // en minutos
                $table->integer('points')->default(100);
                $table->integer('order')->default(0); // Para ordenar dentro de una clase
                $table->json('evaluation_criteria')->nullable(); // Criterios de evaluación
                $table->text('solution_guide')->nullable(); // Guía de solución para el profesor
                $table->timestamps();
            });
        }

        // Ejercicios dentro del desafío (un desafío puede tener múltiples ejercicios)
        if (!Schema::hasTable('challenge_exercises')) {
            Schema::create('challenge_exercises', function (Blueprint $table) {
                $table->id();
                $table->foreignId('challenge_id')->constrained('teaching_challenges')->onDelete('cascade');
                $table->string('title', 150);
                $table->text('description')->nullable();
                $table->text('instructions');
                $table->text('hints')->nullable(); // Pistas para ayudar a los estudiantes
                $table->text('starter_code')->nullable(); // Código inicial para Python
                $table->text('solution_code')->nullable(); // Código de solución para Python
                $table->text('example_prompt')->nullable(); // Ejemplo de prompt para desafíos de IA
                $table->json('test_cases')->nullable(); // Casos de prueba para código Python
                $table->json('evaluation_criteria')->nullable(); // Criterios específicos de este ejercicio
                $table->integer('order')->default(0);
                $table->integer('points')->default(10);
                $table->timestamps();
            });
        }

        // Progreso de estudiantes en desafíos
        if (!Schema::hasTable('challenge_student_progress')) {
            Schema::create('challenge_student_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('challenge_id')->constrained('teaching_challenges')->onDelete('cascade');
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->unsignedBigInteger('student_id');
                    $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
                } else {
                    $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                }
                
                $table->integer('completed_exercises')->default(0);
                $table->integer('total_exercises')->default(0);
                $table->integer('score')->default(0);
                $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();
                
                $table->unique(['challenge_id', 'student_id']);
            });
        }

        // Resoluciones de ejercicios
        if (!Schema::hasTable('exercise_submissions')) {
            Schema::create('exercise_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exercise_id')->constrained('challenge_exercises')->onDelete('cascade');
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->unsignedBigInteger('student_id');
                    $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
                } else {
                    $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                }
                
                $table->text('submitted_code')->nullable(); // Para Python
                $table->text('submitted_prompt')->nullable(); // Para IA
                $table->text('execution_output')->nullable(); // Salida de la ejecución del código
                $table->text('ai_response')->nullable(); // Respuesta de la IA al prompt
                $table->integer('score')->default(0);
                $table->text('feedback')->nullable(); // Feedback automático o del profesor
                $table->enum('status', ['submitted', 'graded', 'rejected'])->default('submitted');
                $table->integer('attempt_number')->default(1);
                $table->timestamps();
            });
        }

        // Tabla para la gestión de estadísticas y análisis
        if (!Schema::hasTable('challenge_analytics')) {
            Schema::create('challenge_analytics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('challenge_id')->constrained('teaching_challenges')->onDelete('cascade');
                $table->integer('total_students')->default(0);
                $table->integer('started_count')->default(0);
                $table->integer('completed_count')->default(0);
                $table->float('average_score')->default(0);
                $table->integer('average_time_minutes')->default(0);
                $table->json('completion_by_day')->nullable(); // Estadísticas de finalización por día
                $table->json('difficulty_metrics')->nullable(); // Métricas de dificultad basadas en intentos
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_analytics');
        Schema::dropIfExists('exercise_submissions');
        Schema::dropIfExists('challenge_student_progress');
        Schema::dropIfExists('challenge_exercises');
        Schema::dropIfExists('teaching_challenges');
    }
};
