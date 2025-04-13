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
        if (!Schema::hasTable('teaching_challenges')) {
            Schema::create('teaching_challenges', function (Blueprint $table) {
                $table->id();
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
                } else {
                    $table->unsignedBigInteger('created_by')->nullable();
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                }
                
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('objectives')->nullable();
                $table->text('instructions')->nullable();
                $table->text('requirements')->nullable();
                $table->string('difficulty_level')->default('intermediate'); // beginner, intermediate, advanced
                $table->string('status')->default('draft'); // draft, published, archived
                $table->boolean('is_public')->default(false);
                $table->integer('duration_minutes')->default(60);
                $table->text('learning_outcomes')->nullable();
                $table->text('teacher_notes')->nullable();
                $table->json('tags')->nullable();
                $table->boolean('requires_approval')->default(false);
                $table->integer('max_attempts')->default(3);
                $table->integer('points_awarded')->default(100);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_challenges');
    }
};
