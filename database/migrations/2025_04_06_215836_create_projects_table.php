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
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->text('blocks_xml');
            $table->text('generated_code')->nullable();
            $table->string('thumbnail_url', 255)->nullable();
            $table->boolean('is_public')->default(false);
            $table->unsignedBigInteger('class_id')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('class_id')->references('class_id')->on('classrooms')->onDelete('set null');
            $table->index('user_id', 'idx_user_projects');
            $table->index('is_public', 'idx_public_projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
