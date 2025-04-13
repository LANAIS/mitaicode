<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHackathonTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hackathon:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea las tablas necesarias para el módulo de hackathones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Instalando módulo de Hackathones...');
        
        // Verificar si ya existen las tablas
        if (Schema::hasTable('hackathons')) {
            if ($this->confirm('Las tablas de hackathones ya existen. ¿Deseas reiniciarlas? Esto eliminará TODOS los datos existentes.')) {
                // Eliminar las tablas en orden inverso de dependencia
                $this->info('Eliminando tablas existentes...');
                
                Schema::dropIfExists('hackathon_messages');
                Schema::dropIfExists('hackathon_deliverables');
                Schema::dropIfExists('hackathon_judges');
                Schema::dropIfExists('hackathon_rounds');
                Schema::dropIfExists('hackathon_team_user');
                Schema::dropIfExists('hackathon_teams');
                Schema::dropIfExists('hackathons');
                
                $this->info('Tablas eliminadas correctamente.');
            } else {
                $this->info('Operación cancelada. Las tablas no se modificaron.');
                return 0;
            }
        }
        
        try {
            // Ejecutar las migraciones específicas
            $this->info('Creando tablas para hackathones...');
            
            $migrationFiles = [
                '2023_06_12_create_hackathons_table.php',
                '2023_07_02_create_hackathon_teams_table.php',
                '2023_07_05_create_hackathon_rounds_table.php',
                '2025_04_07_031315_create_hackathon_judges_table.php'
            ];
            
            foreach ($migrationFiles as $file) {
                $this->info("Ejecutando migración: $file");
                Artisan::call('migrate', [
                    '--path' => "database/migrations/$file",
                    '--force' => true,
                ]);
                $this->info(Artisan::output());
            }
            
            $this->info('¡Módulo de Hackathones instalado correctamente!');
            $this->info('Ahora puedes acceder a las funcionalidades de hackathones en la plataforma.');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error al crear las tablas: ' . $e->getMessage());
            return 1;
        }
    }
}
