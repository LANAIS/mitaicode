<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Challenge;
use App\Models\DailyStatistic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PopulateAnalyticsData extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'analytics:populate-data {--days=30 : Número de días para generar datos}';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Genera datos de prueba para la sección de analíticas';

    /**
     * Ejecutar el comando de consola.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        $this->info("Generando datos para los últimos {$days} días...");
        
        // 1. Crear usuario de prueba si no existe
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'last_login_at' => now()->timestamp
            ]);
            $this->info("Usuario administrador creado");
        }
        
        // 2. Crear desafíos de prueba
        $challengeTypes = ['coding', 'quiz', 'project'];
        $challengeDifficulties = ['beginner', 'intermediate', 'advanced'];
        
        if (Challenge::count() < 5) {
            for ($i = 1; $i <= 10; $i++) {
                Challenge::create([
                    'title' => "Desafío de prueba {$i}",
                    'description' => "Descripción del desafío {$i}",
                    'difficulty' => $challengeDifficulties[array_rand($challengeDifficulties)],
                    'type' => $challengeTypes[array_rand($challengeTypes)],
                    'points' => rand(10, 100),
                    'duration' => rand(15, 60),
                    'created_by' => $admin->id,
                    'is_published' => true
                ]);
            }
            $this->info("10 desafíos de prueba creados");
        }
        
        // 3. Generar estadísticas diarias simuladas
        $challenges = Challenge::all();
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days - 1);
        
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $this->info("Generando datos para: {$dateStr}");
            
            // Registros de usuarios (entre 5 y 20 por día)
            $newUsers = rand(5, 20);
            DailyStatistic::incrementStat($dateStr, 'user_registrations', null, $newUsers);
            
            // Usuarios activos (entre 20 y 50 por día)
            $activeUsers = rand(20, 50);
            DailyStatistic::incrementStat($dateStr, 'user_activity', null, $activeUsers);
            
            // Desafíos iniciados (entre 10 y 30 por día)
            $startedChallenges = rand(10, 30);
            DailyStatistic::incrementStat($dateStr, 'challenge_starts', null, $startedChallenges);
            
            // Desafíos completados (entre 5 y 15 por día)
            $completedChallenges = rand(5, 15);
            DailyStatistic::incrementStat($dateStr, 'challenge_completions', null, $completedChallenges);
            
            // Estadísticas por tipo de desafío
            foreach ($challengeTypes as $type) {
                $typeCount = rand(1, 5);
                DailyStatistic::incrementStat($dateStr, 'challenge_completions_by_type', $type, $typeCount);
            }
            
            // También crear algunos registros en challenge_user para simular participaciones
            if ($date->diffInDays(Carbon::today()) % 3 == 0) {
                $challengeId = $challenges->random()->id;
                
                DB::table('challenge_user')->insert([
                    'challenge_id' => $challengeId,
                    'user_id' => $admin->id,
                    'is_completed' => true,
                    'progress' => 100,
                    'points_earned' => rand(10, 50),
                    'started_at' => $date->copy()->subHours(rand(1, 10)),
                    'completed_at' => $date,
                    'status' => 'completed',
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            }
        }
        
        $this->info("Datos de analíticas generados correctamente para {$days} días");
        return Command::SUCCESS;
    }
}
