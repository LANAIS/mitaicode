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
        Schema::table('challenge_student_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('challenge_student_progress', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('started_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenge_student_progress', function (Blueprint $table) {
            if (Schema::hasColumn('challenge_student_progress', 'last_activity_at')) {
                $table->dropColumn('last_activity_at');
            }
        });
    }
};
