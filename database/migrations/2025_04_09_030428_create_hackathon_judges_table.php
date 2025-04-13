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
        if (!Schema::hasTable('hackathon_judges')) {
            Schema::create('hackathon_judges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hackathon_id')->constrained('hackathons')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->boolean('is_lead_judge')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->unique(['hackathon_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathon_judges');
    }
};
