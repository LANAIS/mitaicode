<?php

namespace App\Console\Commands;

use App\Models\UserStreak;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateUserStreaks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streaks:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza las rachas de usuarios y reinicia aquellas inactivas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de actualización de rachas...');
        
        try {
            // Procesar todas las rachas
            UserStreak::processStreaks();
            
            $this->info('Rachas actualizadas correctamente.');
            Log::info('Comando streaks:update ejecutado correctamente.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error al actualizar rachas: ' . $e->getMessage());
            Log::error('Error en comando streaks:update: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
