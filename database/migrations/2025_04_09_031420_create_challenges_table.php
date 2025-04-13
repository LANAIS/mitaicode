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
        if (!Schema::hasTable('challenges')) {
            Schema::create('challenges', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('objectives')->nullable();
                $table->string('level')->default('beginner'); // beginner, intermediate, advanced
                $table->integer('points')->default(100);
                $table->string('status')->default('active'); // active, inactive, draft
                $table->string('image_url')->nullable();
                $table->string('estimated_time')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
