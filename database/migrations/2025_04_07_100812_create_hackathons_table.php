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
        if (!Schema::hasTable('hackathons')) {
            Schema::create('hackathons', function (Blueprint $table) {
                $table->id('hackathon_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->foreignId('created_by')->constrained('users', 'id')->onDelete('cascade');
                $table->integer('max_participants')->default(50);
                $table->integer('max_teams')->default(10);
                $table->integer('team_size')->default(5);
                $table->integer('current_round')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();

                // Índices
                $table->index('created_by');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathons');
    }
};
