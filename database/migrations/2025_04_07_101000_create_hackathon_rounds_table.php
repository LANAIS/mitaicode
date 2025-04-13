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
        Schema::create('hackathon_rounds', function (Blueprint $table) {
            $table->id('round_id');
            $table->foreignId('hackathon_id')->constrained('hackathons', 'hackathon_id')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('objectives')->nullable();
            $table->text('deliverables')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            
            // Índices
            $table->index('hackathon_id');
            $table->index('order');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_rounds');
    }
};
