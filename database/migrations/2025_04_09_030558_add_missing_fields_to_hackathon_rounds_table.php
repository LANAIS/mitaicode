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
        Schema::table('hackathon_rounds', function (Blueprint $table) {
            if (!Schema::hasColumn('hackathon_rounds', 'objectives')) {
                $table->text('objectives')->nullable();
            }
            
            if (!Schema::hasColumn('hackathon_rounds', 'deliverables')) {
                $table->text('deliverables')->nullable();
            }
            
            if (!Schema::hasColumn('hackathon_rounds', 'order')) {
                $table->integer('order')->default(1);
            }
            
            if (!Schema::hasColumn('hackathon_rounds', 'is_active')) {
                $table->boolean('is_active')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hackathon_rounds', function (Blueprint $table) {
            $table->dropColumnIfExists('objectives');
            $table->dropColumnIfExists('deliverables');
            $table->dropColumnIfExists('order');
            $table->dropColumnIfExists('is_active');
        });
    }
};
