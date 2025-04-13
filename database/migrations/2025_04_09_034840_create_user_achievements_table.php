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
        if (!Schema::hasTable('user_achievements')) {
            Schema::create('user_achievements', function (Blueprint $table) {
                $table->id();
                
                // Verificar las referencias a user_id
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                } else {
                    $table->unsignedBigInteger('user_id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
                
                $table->unsignedBigInteger('achievement_id');
                $table->timestamp('unlocked_at')->useCurrent();
                $table->boolean('is_viewed')->default(false);
                $table->integer('progress')->default(0);
                $table->integer('progress_target')->default(100);
                $table->boolean('is_completed')->default(false);
                $table->timestamps();
                
                // Claves foráneas
                if (Schema::hasTable('badges')) {
                    $table->foreign('achievement_id')->references('id')->on('badges')->onDelete('cascade');
                }
                
                // Índices
                $table->index(['user_id', 'achievement_id']);
                
                // Solo crear un índice único si no estamos recreando la tabla con datos existentes
                if (Schema::hasTable('badges')) {
                    $table->unique(['user_id', 'achievement_id']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};
