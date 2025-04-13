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
        if (!Schema::hasTable('challenge_exercises')) {
            Schema::create('challenge_exercises', function (Blueprint $table) {
                $table->id();
                $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                $table->text('hints')->nullable();
                $table->text('solution')->nullable();
                $table->integer('order')->default(0);
                $table->integer('points')->default(10);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenge_exercises');
    }
};
