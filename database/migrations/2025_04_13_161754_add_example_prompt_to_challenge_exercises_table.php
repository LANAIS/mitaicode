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
        Schema::table('challenge_exercises', function (Blueprint $table) {
            if (!Schema::hasColumn('challenge_exercises', 'example_prompt')) {
                $table->text('example_prompt')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenge_exercises', function (Blueprint $table) {
            if (Schema::hasColumn('challenge_exercises', 'example_prompt')) {
                $table->dropColumn('example_prompt');
            }
        });
    }
};
