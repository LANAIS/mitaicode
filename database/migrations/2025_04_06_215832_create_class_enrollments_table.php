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
        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->id('enrollment_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('student_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('class_id')->references('class_id')->on('classrooms')->onDelete('cascade');
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->unique(['class_id', 'student_id'], 'unique_enrollment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
