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
        if (!Schema::hasTable('classrooms')) {
            Schema::create('classrooms', function (Blueprint $table) {
                $table->id();
                $table->string('class_name');
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('class_code')->unique();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
