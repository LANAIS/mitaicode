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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id('class_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('class_name', 100);
            $table->text('description')->nullable();
            $table->string('class_code', 10)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('teacher_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->index('class_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
