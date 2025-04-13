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
        if (!Schema::hasTable('store_items')) {
            Schema::create('store_items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->string('type'); // avatar, badge, background, etc.
                $table->integer('price');
                $table->integer('duration')->nullable(); // en días, null = permanente
                $table->boolean('is_limited')->default(false);
                $table->integer('stock')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('rarity')->default('common'); // common, uncommon, rare, epic, legendary
                $table->integer('level_required')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_items');
    }
};
