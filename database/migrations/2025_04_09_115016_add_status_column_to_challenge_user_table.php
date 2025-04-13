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
        // Esta tabla se crea posteriormente en 2025_04_09_115847_create_challenge_user_table.php
        // con la columna status incluida, así que no es necesario ejecutar esta migración
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario hacer nada, ya que la columna se elimina con la tabla
    }
};
