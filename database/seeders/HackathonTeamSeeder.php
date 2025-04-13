<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hackathon;
use App\Models\User;
use App\Models\HackathonTeam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HackathonTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que las tablas necesarias existan
        if (!Schema::hasTable('hackathons') || !Schema::hasTable('users')) {
            $this->command->error('Las tablas necesarias no existen. Ejecuta las migraciones primero.');
            return;
        }

        // Verificar que existan hackathones
        $hackathons = Hackathon::all();
        if ($hackathons->isEmpty()) {
            $this->command->error('No hay hackathones en la base de datos. Ejecuta HackathonSeeder primero.');
            return;
        }

        // Obtener estudiantes
        $students = User::where('role', 'student')->get();
        if ($students->count() < 5) {
            $this->command->warn('Hay pocos estudiantes para crear equipos completos.');
        }

        // Limpiar datos existentes (opcional)
        DB::table('hackathon_team_user')->truncate();
        HackathonTeam::truncate();

        $this->command->info('Creando equipos para hackathones...');

        // Para cada hackathon activo o pendiente, crear varios equipos
        foreach ($hackathons as $hackathon) {
            if ($hackathon->status != 'finished') {
                $numTeams = min(3, $hackathon->max_teams); // Crear hasta 3 equipos por hackathon
                
                for ($i = 1; $i <= $numTeams; $i++) {
                    $team = HackathonTeam::create([
                        'hackathon_id' => $hackathon->id,
                        'name' => 'Equipo ' . $i . ' - ' . substr($hackathon->title, 0, 15),
                        'description' => 'Equipo de estudiantes para el hackathon ' . $hackathon->title,
                        'is_winner' => false
                    ]);
                    
                    // Asignar hasta 4 estudiantes aleatorios al equipo
                    $teamStudents = $students->random(min(4, $students->count()));
                    $isFirst = true;
                    
                    foreach ($teamStudents as $student) {
                        // Verificar que el ID del estudiante no sea nulo
                        if ($student->user_id) {
                            // Insertar relación en la tabla pivot
                            DB::table('hackathon_team_user')->insert([
                                'team_id' => $team->id,
                                'user_id' => $student->user_id,
                                'is_leader' => $isFirst, // El primer estudiante es el líder
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            
                            $isFirst = false;
                        } else {
                            $this->command->warn("Usuario {$student->username} no tiene ID válido.");
                        }
                    }
                    
                    $this->command->line("Creado equipo '{$team->name}' con " . count($teamStudents) . " miembros.");
                }
            }
        }
        
        $this->command->info('Equipos de hackathones creados exitosamente.');
    }
}
