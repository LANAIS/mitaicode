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
            if (!Schema::hasColumn('store_items', 'category')) {
                $table->string('category')->nullable();
            }
            
            if (!Schema::hasColumn('store_items', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }
            
            if (!Schema::hasColumn('store_items', 'description') && Schema::hasTable('store_items')) {
                $table->text('description')->nullable();
            }
            
            if (!Schema::hasColumn('store_items', 'image_url') && Schema::hasTable('store_items')) {
                $table->string('image_url')->nullable();
            }
            
            if (Schema::hasColumn('store_items', 'min_level') && !Schema::hasColumn('store_items', 'level_required')) {
                Schema::table('store_items', function (Blueprint $table) {
                    $table->renameColumn('min_level', 'level_required');
                });
            } else if (!Schema::hasColumn('store_items', 'level_required')) {
                $table->integer('level_required')->default(1);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_items', function (Blueprint $table) {
            $table->dropColumnIfExists('category');
            $table->dropColumnIfExists('slug');
            $table->dropColumnIfExists('description');
            $table->dropColumnIfExists('image_url');
            
            if (Schema::hasColumn('store_items', 'level_required') && !Schema::hasColumn('store_items', 'min_level')) {
                Schema::table('store_items', function (Blueprint $table) {
                    $table->renameColumn('level_required', 'min_level');
                });
            } else {
                $table->dropColumnIfExists('level_required');
            }
        });
    }
};
