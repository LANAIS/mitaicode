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
        Schema::create('user_avatars', function (Blueprint $table) {
            $table->id('avatar_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('base_avatar')->default('default');
            $table->string('skin_color')->default('#F5D0A9');
            $table->string('hair_style')->default('default');
            $table->string('hair_color')->default('#6F4E37');
            $table->string('eye_type')->default('default');
            $table->string('eye_color')->default('#6F4E37');
            $table->string('mouth_type')->default('default');
            $table->string('outfit')->default('default');
            $table->string('accessory')->nullable();
            $table->string('background')->default('default');
            $table->string('frame')->nullable();
            $table->json('custom_elements')->nullable();
            $table->string('current_rank')->default('Novato');
            $table->string('current_title')->nullable();
            $table->timestamps();
            
            // Índice único para asegurar que cada usuario tenga solo un avatar
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_avatars');
    }
};
