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
        Schema::create('user_inventories', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('store_items', 'item_id')->onDelete('cascade');
            $table->boolean('is_equipped')->default(false);
            $table->boolean('is_used')->default(false);
            $table->dateTime('acquired_at');
            $table->dateTime('expires_at')->nullable();
            $table->json('custom_properties')->nullable();
            $table->timestamps();
            
            // Índice único para evitar duplicados
            $table->unique(['user_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_inventories');
    }
};
