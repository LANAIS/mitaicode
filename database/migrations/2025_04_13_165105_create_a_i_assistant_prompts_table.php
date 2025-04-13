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
        Schema::create('ai_assistant_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'idea_generator', 'exercise_variant', 'quality_checker'
            $table->text('prompt_template');
            $table->text('description')->nullable();
            $table->json('parameters')->nullable();
            $table->json('example_outputs')->nullable();
            $table->string('category')->nullable(); // Por ejemplo: 'python', 'ai_prompt', etc.
            $table->string('difficulty_level')->nullable(); // 'principiante', 'intermedio', 'avanzado'
            $table->foreignId('created_by')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // Para prompts pre-definidos del sistema
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_assistant_prompts');
    }
};
