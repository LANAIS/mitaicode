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
        if (Schema::hasTable('hackathon_teams')) {
            // Solo si la tabla ya existe, añade las columnas
            if (!Schema::hasColumn('hackathon_teams', 'is_winner')) {
                Schema::table('hackathon_teams', function (Blueprint $table) {
                    $table->boolean('is_winner')->default(false)->after('description');
                    $table->string('position')->nullable()->after('is_winner');
                });
            }
        } else {
            // Creamos la tabla si no existe
            Schema::create('hackathon_teams', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hackathon_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_winner')->default(false);
                $table->string('position')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('hackathon_teams')) {
            if (Schema::hasColumn('hackathon_teams', 'is_winner')) {
                Schema::table('hackathon_teams', function (Blueprint $table) {
                    $table->dropColumn('is_winner');
                    $table->dropColumn('position');
                });
            }
        }
    }
}; 