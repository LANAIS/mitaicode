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
        if (!Schema::hasTable('hackathon_teams')) {
            Schema::create('hackathon_teams', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hackathon_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_winner')->default(false);
                $table->string('position')->nullable();
                $table->string('project_name')->nullable();
                $table->text('project_description')->nullable();
                $table->timestamps();
            });
        } else {
            // La tabla ya existe, verificamos si debemos añadir las columnas faltantes
            if (!Schema::hasColumn('hackathon_teams', 'project_name')) {
                Schema::table('hackathon_teams', function (Blueprint $table) {
                    $table->string('project_name')->nullable();
                    $table->text('project_description')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_teams');
    }
}; 