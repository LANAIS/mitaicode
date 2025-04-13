<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix {--batch=1 : El número de batch para asignar a las migraciones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca las migraciones pendientes como ya ejecutadas sin perder datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingMigrations = $this->getPendingMigrations();
        
        if (empty($pendingMigrations)) {
            $this->info('No hay migraciones pendientes para arreglar.');
            return 0;
        }
        
        $this->info('Se encontraron ' . count($pendingMigrations) . ' migraciones pendientes.');
        
        $batch = $this->option('batch') ?? $this->getLastBatchNumber() + 1;
        $this->info('Se marcarán como ejecutadas en el batch: ' . $batch);
        
        $this->markMigrationsAsRun($pendingMigrations, $batch);
        
        $this->info('Migraciones marcadas como ejecutadas correctamente.');
        return 0;
    }
    
    /**
     * Obtiene las migraciones pendientes.
     *
     * @return array
     */
    protected function getPendingMigrations()
    {
        // Comprobar si la tabla de migraciones existe
        if (!Schema::hasTable('migrations')) {
            $this->error('La tabla de migraciones no existe. Ejecute primero php artisan migrate:install');
            return [];
        }
        
        // Obtener todas las migraciones de los archivos
        $files = glob(database_path('migrations/*.php'));
        $pendingMigrations = [];
        
        foreach ($files as $file) {
            $migration = basename($file, '.php');
            
            // Comprobar si la migración ya está en la base de datos
            $exists = DB::table('migrations')->where('migration', $migration)->exists();
            
            if (!$exists) {
                $pendingMigrations[] = $migration;
            }
        }
        
        return $pendingMigrations;
    }
    
    /**
     * Obtiene el último número de batch.
     *
     * @return int
     */
    protected function getLastBatchNumber()
    {
        $lastBatch = DB::table('migrations')->max('batch') ?? 0;
        return (int) $lastBatch;
    }
    
    /**
     * Marca las migraciones como ejecutadas.
     *
     * @param array $migrations
     * @param int $batch
     * @return void
     */
    protected function markMigrationsAsRun($migrations, $batch)
    {
        foreach ($migrations as $migration) {
            $this->info('Marcando ' . $migration . ' como ejecutada...');
            
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch
            ]);
            
            $this->line('✓ ' . $migration);
        }
    }
}
