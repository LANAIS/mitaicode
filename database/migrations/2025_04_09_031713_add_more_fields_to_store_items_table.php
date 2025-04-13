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
        Schema::table('store_items', function (Blueprint $table) {
            // Renombrar columnas si existen
            if (Schema::hasColumn('store_items', 'is_available') && !Schema::hasColumn('store_items', 'is_active')) {
                $table->renameColumn('is_available', 'is_active');
            }
            
            // Solo renombrar si image_path existe y image_url NO existe
            if (Schema::hasColumn('store_items', 'image_path') && !Schema::hasColumn('store_items', 'image_url')) {
                $table->renameColumn('image_path', 'image_url');
            }
            
            // Añadir columnas que faltan
            if (!Schema::hasColumn('store_items', 'is_limited')) {
                $table->boolean('is_limited')->default(false);
            }
            
            if (!Schema::hasColumn('store_items', 'stock')) {
                $table->integer('stock')->nullable();
            }
            
            if (!Schema::hasColumn('store_items', 'duration')) {
                $table->integer('duration')->nullable();
            }
            
            if (!Schema::hasColumn('store_items', 'rarity')) {
                $table->string('rarity')->default('common');
            }
            
            if (!Schema::hasColumn('store_items', 'min_level') && !Schema::hasColumn('store_items', 'level_required')) {
                $table->integer('level_required')->default(1);
            } else if (Schema::hasColumn('store_items', 'min_level') && !Schema::hasColumn('store_items', 'level_required')) {
                $table->renameColumn('min_level', 'level_required');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_items', function (Blueprint $table) {
            if (Schema::hasColumn('store_items', 'is_active') && !Schema::hasColumn('store_items', 'is_available')) {
                $table->renameColumn('is_active', 'is_available');
            }
            
            if (Schema::hasColumn('store_items', 'image_url') && !Schema::hasColumn('store_items', 'image_path')) {
                $table->renameColumn('image_url', 'image_path');
            }
            
            // Usar un enfoque de seguridad para evitar errores
            $columnsToCheck = ['is_limited', 'stock', 'duration', 'rarity', 'level_required'];
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('store_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
