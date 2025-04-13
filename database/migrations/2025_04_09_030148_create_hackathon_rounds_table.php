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
        if (!Schema::hasTable('hackathon_rounds')) {
            Schema::create('hackathon_rounds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hackathon_id')->constrained('hackathons');
                $table->string('name');
                $table->text('description')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->text('objectives')->nullable();
                $table->text('deliverables')->nullable();
                $table->integer('order');
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_rounds');
    }
};
