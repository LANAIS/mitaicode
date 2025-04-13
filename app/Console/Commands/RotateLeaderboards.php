<?php

namespace App\Console\Commands;

use App\Services\GamificationService;
use Illuminate\Console\Command;

class RotateLeaderboards extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'gamification:rotate-leaderboards';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Rota las tablas de puntajes semanales y mensuales cuando sea necesario';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle(GamificationService $gamificationService)
    {
        $this->info('Verificando tablas de puntajes para rotación...');
        
        $gamificationService->rotateLeaderboards();
        
        $this->info('¡Rotación de tablas de puntajes completada!');
        
        return Command::SUCCESS;
    }
}
