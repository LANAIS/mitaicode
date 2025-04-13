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
        if (!Schema::hasTable('challenges')) {
            Schema::create('challenges', function (Blueprint $table) {
                $table->id();
                $table->string('title', 191);
                $table->text('description')->nullable();
                $table->text('content')->nullable();
                $table->string('image')->nullable();
                $table->string('difficulty')->default('beginner'); // beginner, intermediate, advanced
                $table->string('type')->nullable(); // coding, quiz, etc.
                $table->integer('points')->default(0);
                $table->integer('duration')->nullable(); // en minutos
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_published')->default(false);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
