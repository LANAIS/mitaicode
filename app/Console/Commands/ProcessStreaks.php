<?php

namespace App\Console\Commands;

use App\Services\GamificationService;
use Illuminate\Console\Command;

class ProcessStreaks extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'gamification:process-streaks';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Procesa las rachas de usuarios y reinicia aquellas inactivas';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle(GamificationService $gamificationService)
    {
        $this->info('Iniciando procesamiento de rachas...');
        
        $gamificationService->processStreaks();
        
        $this->info('¡Procesamiento de rachas completado!');
        
        return Command::SUCCESS;
    }
}
