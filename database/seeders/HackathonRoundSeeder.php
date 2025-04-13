<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hackathon;
use App\Models\HackathonRound;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class HackathonRoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que las tablas necesarias existan
        if (!Schema::hasTable('hackathons') || !Schema::hasTable('hackathon_rounds')) {
            $this->command->error('Las tablas necesarias no existen. Ejecuta las migraciones primero.');
            return;
        }

        // Verificar que existan hackathones
        $hackathons = Hackathon::all();
        if ($hackathons->isEmpty()) {
            $this->command->error('No hay hackathones en la base de datos. Ejecuta HackathonSeeder primero.');
            return;
        }

        // Limpiar datos existentes
        HackathonRound::truncate();

        $this->command->info('Creando rondas para hackathones...');

        // Para cada hackathon, crear rondas
        foreach ($hackathons as $hackathon) {
            $numRounds = 3; // Número estándar de rondas
            $roundDuration = ceil($hackathon->start_date->diffInDays($hackathon->end_date) / $numRounds);
            
            for ($i = 1; $i <= $numRounds; $i++) {
                $startDate = $hackathon->start_date->copy()->addDays(($i - 1) * $roundDuration);
                $endDate = $i < $numRounds ? 
                    $hackathon->start_date->copy()->addDays($i * $roundDuration - 1) : 
                    $hackathon->end_date;
                
                $status = 'pending';
                if ($hackathon->status === 'active') {
                    if ($i === 1) {
                        $status = 'active';
                    } elseif ($i > 1) {
                        $status = 'pending';
                    }
                } elseif ($hackathon->status === 'finished') {
                    $status = 'completed';
                }
                
                $round = HackathonRound::create([
                    'hackathon_id' => $hackathon->id,
                    'order' => $i,
                    'name' => 'Ronda ' . $i . ' - ' . ($i === 1 ? 'Ideación' : ($i === 2 ? 'Desarrollo' : 'Presentación Final')),
                    'description' => $this->getRoundDescription($i),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'is_active' => $hackathon->status === 'active' && $i === 1,
                    'objectives' => 'Objetivos de la ronda ' . $i,
                    'deliverables' => $this->getRoundRequirements($i),
                ]);
                
                $this->command->line("Creada ronda '{$round->name}' para el hackathon '{$hackathon->title}'");
            }
            
            // Actualizar el hackathon con la ronda actual
            if ($hackathon->status === 'active') {
                $hackathon->current_round = 1;
                $hackathon->save();
            } elseif ($hackathon->status === 'finished') {
                $hackathon->current_round = 3;
                $hackathon->save();
            }
        }
        
        $this->command->info('Rondas de hackathones creadas exitosamente.');
    }
    
    /**
     * Obtener descripción según el número de ronda
     */
    private function getRoundDescription($roundNumber)
    {
        switch ($roundNumber) {
            case 1:
                return 'En esta primera ronda, los equipos deben presentar una propuesta de su idea y cómo planean abordar el reto. Se evaluará la originalidad, viabilidad y relevancia.';
            case 2:
                return 'En la segunda ronda, los equipos deben presentar un prototipo o avance de su proyecto. Se evaluará el progreso técnico y la implementación de la idea.';
            case 3:
                return 'En la ronda final, los equipos deben presentar su proyecto completo y hacer una demostración. Se evaluará el producto final, la presentación y el potencial de impacto.';
            default:
                return 'Ronda del hackathon';
        }
    }
    
    /**
     * Obtener requisitos según el número de ronda
     */
    private function getRoundRequirements($roundNumber)
    {
        switch ($roundNumber) {
            case 1:
                return json_encode([
                    'Documento de propuesta (PDF, máx. 5 páginas)',
                    'Presentación de la idea (PowerPoint, máx. 10 diapositivas)',
                    'Plan de desarrollo'
                ]);
            case 2:
                return json_encode([
                    'Prototipo funcional',
                    'Documentación técnica',
                    'Video de demostración (máx. 3 minutos)'
                ]);
            case 3:
                return json_encode([
                    'Producto final',
                    'Presentación del proyecto',
                    'Documentación completa',
                    'Video de demostración (máx. 5 minutos)'
                ]);
            default:
                return json_encode(['Entregable general']);
        }
    }
}
