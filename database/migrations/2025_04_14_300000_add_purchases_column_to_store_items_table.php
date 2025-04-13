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
            if (!Schema::hasColumn('store_items', 'purchases')) {
                $table->integer('purchases')->default(0)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_items', function (Blueprint $table) {
            if (Schema::hasColumn('store_items', 'purchases')) {
                $table->dropColumn('purchases');
            }
        });
    }
}; 