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
        Schema::create('badges', function (Blueprint $table) {
            $table->id('badge_id');
            $table->string('badge_code', 50)->unique();
            $table->string('name', 100);
            $table->text('description');
            $table->string('image_url', 255);
            $table->text('criteria');
            $table->timestamps();
            
            $table->index('badge_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
