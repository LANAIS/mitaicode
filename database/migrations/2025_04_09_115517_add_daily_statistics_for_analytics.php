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
        Schema::create('daily_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('metric_type', 100);
            $table->string('metric_value', 100)->nullable();
            $table->integer('count')->default(0);
            $table->timestamps();

            $table->index(['metric_type', 'metric_value'], 'stats_metric_index');
            $table->index(['date', 'metric_type'], 'stats_date_metric_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_statistics');
    }
};
