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
        Schema::create('missions', function (Blueprint $table) {
            $table->id('mission_id');
            $table->string('title', 100);
            $table->text('description');
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced']);
            $table->string('category', 50);
            $table->integer('xp_reward')->default(0);
            $table->string('badge_reward', 50)->nullable();
            $table->text('requirements')->nullable();
            $table->text('success_criteria');
            $table->text('starter_blocks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('difficulty');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
