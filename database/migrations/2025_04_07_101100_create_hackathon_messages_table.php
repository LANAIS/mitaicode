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
        if (!Schema::hasTable('hackathon_messages')) {
            Schema::create('hackathon_messages', function (Blueprint $table) {
                $table->id('message_id');
                
                // Verificar las claves primarias correctas
                if (Schema::hasColumn('hackathon_teams', 'team_id')) {
                    $table->foreignId('team_id')->constrained('hackathon_teams', 'team_id')->onDelete('cascade');
                } else {
                    $table->foreignId('team_id')->constrained('hackathon_teams', 'id')->onDelete('cascade');
                }
                
                if (Schema::hasColumn('users', 'user_id')) {
                    $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
                } else {
                    $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');
                }
                
                $table->text('content');
                $table->string('type')->default('text'); // text, file, code, etc.
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_type')->nullable();
                $table->integer('file_size')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Índices
                $table->index('team_id');
                $table->index('user_id');
                $table->index('type');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_messages');
    }
}; 