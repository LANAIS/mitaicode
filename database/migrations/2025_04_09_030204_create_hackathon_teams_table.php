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
                $table->foreignId('hackathon_id')->constrained('hackathons');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_winner')->default(false);
                $table->string('position')->nullable();
                $table->string('project_name')->nullable();
                $table->text('project_description')->nullable();
                $table->timestamps();
            });
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
