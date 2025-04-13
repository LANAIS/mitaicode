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
        Schema::table('hackathons', function (Blueprint $table) {
            if (!Schema::hasColumn('hackathons', 'created_by')) {
                $table->foreignId('created_by')->nullable();
            }
            
            if (!Schema::hasColumn('hackathons', 'max_participants')) {
                $table->integer('max_participants')->default(50);
            }
            
            if (!Schema::hasColumn('hackathons', 'max_teams')) {
                $table->integer('max_teams')->default(10);
            }
            
            if (!Schema::hasColumn('hackathons', 'team_size')) {
                $table->integer('team_size')->default(5);
            }
            
            if (!Schema::hasColumn('hackathons', 'current_round')) {
                $table->integer('current_round')->nullable();
            }
            
            if (!Schema::hasColumn('hackathons', 'status')) {
                $table->string('status')->default('pending');
            }
            
            if (!Schema::hasColumn('hackathons', 'image')) {
                $table->string('image')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hackathons', function (Blueprint $table) {
            $table->dropColumnIfExists('created_by');
            $table->dropColumnIfExists('max_participants');
            $table->dropColumnIfExists('max_teams');
            $table->dropColumnIfExists('team_size');
            $table->dropColumnIfExists('current_round');
            $table->dropColumnIfExists('status');
            $table->dropColumnIfExists('image');
        });
    }
};
