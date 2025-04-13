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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id('profile_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->integer('level')->default(1);
            $table->integer('xp_points')->default(0);
            $table->integer('total_blocks_used')->default(0);
            $table->integer('total_missions_completed')->default(0);
            $table->string('parent_email', 100)->nullable();
            $table->integer('age')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
