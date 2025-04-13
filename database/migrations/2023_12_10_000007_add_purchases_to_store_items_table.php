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
        // Solo ejecutar si la tabla store_items existe
        if (Schema::hasTable('store_items')) {
            Schema::table('store_items', function (Blueprint $table) {
                // Verificar si ya existe purchases
                if (!Schema::hasColumn('store_items', 'purchases')) {
                    // Verificar qué columna existe para usar como referencia
                    if (Schema::hasColumn('store_items', 'level_required')) {
                        $table->integer('purchases')->default(0)->after('level_required');
                    } else if (Schema::hasColumn('store_items', 'min_level')) {
                        $table->integer('purchases')->default(0)->after('min_level');
                    } else {
                        // Si ninguna de las dos columnas existe, simplemente agregamos sin after
                        $table->integer('purchases')->default(0);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('store_items') && Schema::hasColumn('store_items', 'purchases')) {
            Schema::table('store_items', function (Blueprint $table) {
                $table->dropColumn('purchases');
            });
        }
    }
}; 