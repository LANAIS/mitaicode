<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class HackathonInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hackathon:install-fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instala el módulo de hackathones con las migraciones existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Instalando módulo de Hackathones (versión mejorada)...');
        
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
            // Buscar las migraciones relacionadas con hackathones
            $this->info('Buscando migraciones relacionadas con hackathones...');
            
            $migrationPath = database_path('migrations');
            $migrationFiles = collect(File::files($migrationPath))
                ->filter(function ($file) {
                    return str_contains($file->getFilename(), 'hackathon');
                })
                ->map(function ($file) {
                    return $file->getFilename();
                })
                ->toArray();
            
            if (count($migrationFiles) == 0) {
                $this->error('No se encontraron migraciones relacionadas con hackathones.');
                return 1;
            }
            
            $this->info('Se encontraron ' . count($migrationFiles) . ' migraciones relacionadas con hackathones.');
            
            // Ejecutar todas las migraciones
            $this->info('Ejecutando migraciones...');
            
            Artisan::call('migrate', [
                '--force' => true,
            ]);
            
            $this->info(Artisan::output());
            
            $this->info('¡Módulo de Hackathones instalado correctamente!');
            $this->info('Ahora puedes acceder a las funcionalidades de hackathones en la plataforma.');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error al crear las tablas: ' . $e->getMessage());
            return 1;
        }
    }
}
