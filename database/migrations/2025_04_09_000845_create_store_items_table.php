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
        Schema::create('store_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->enum('category', ['avatar', 'badge', 'rank', 'skin', 'special']);
            $table->string('type', 50);
            $table->integer('price');
            $table->integer('level_required')->default(1);
            $table->boolean('is_limited')->default(false);
            $table->integer('stock')->nullable();
            $table->json('effects')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_items');
    }
};
