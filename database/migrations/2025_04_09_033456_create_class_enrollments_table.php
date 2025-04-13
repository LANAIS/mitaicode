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
        if (!Schema::hasTable('class_enrollments')) {
            Schema::create('class_enrollments', function (Blueprint $table) {
                $table->id();
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->unsignedBigInteger('student_id');
                    $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
                } else {
                    $table->unsignedBigInteger('student_id');
                    $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                }
                
                $table->unsignedBigInteger('class_id');
                $table->boolean('is_active')->default(true);
                $table->date('enrolled_at')->nullable();
                $table->date('completed_at')->nullable();
                $table->enum('status', ['active', 'completed', 'dropped'])->default('active');
                $table->timestamps();
                
                $table->foreign('class_id')->references('id')->on('classrooms')->onDelete('cascade');
                
                // Índice para búsquedas frecuentes
                $table->index(['student_id', 'is_active']);
                $table->index(['class_id', 'is_active']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
