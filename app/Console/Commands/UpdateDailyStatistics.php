<?php

namespace App\Console\Commands;

use App\Models\DailyStatistic;
use App\Models\User;
use App\Models\Challenge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateDailyStatistics extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'analytics:update-daily-stats {--date= : Fecha específica para actualizar (YYYY-MM-DD). Por defecto es hoy.}';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Actualiza las estadísticas diarias para la sección de analíticas';

    /**
     * Ejecutar el comando de consola.
     */
    public function handle()
    {
        $dateParam = $this->option('date');
        $targetDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();
        $dateStr = $targetDate->format('Y-m-d');

        $this->info("Actualizando estadísticas para la fecha: {$dateStr}");

        // 1. Registros de usuarios
        $newUsers = User::whereDate('created_at', $dateStr)->count();
        DailyStatistic::incrementStat($dateStr, 'user_registrations', null, $newUsers);
        $this->info("Usuarios nuevos: {$newUsers}");

        // 2. Usuarios activos
        $activeUsers = User::whereDate('last_login_at', $dateStr)->count();
        DailyStatistic::incrementStat($dateStr, 'user_activity', null, $activeUsers);
        $this->info("Usuarios activos: {$activeUsers}");

        // 3. Desafíos completados
        $completedChallenges = DB::table('challenge_user')
            ->whereDate('completed_at', $dateStr)
            ->where('status', 'completed')
            ->count();
        DailyStatistic::incrementStat($dateStr, 'challenge_completions', null, $completedChallenges);
        $this->info("Desafíos completados: {$completedChallenges}");

        // 4. Desafíos iniciados
        $startedChallenges = DB::table('challenge_user')
            ->whereDate('started_at', $dateStr)
            ->count();
        DailyStatistic::incrementStat($dateStr, 'challenge_starts', null, $startedChallenges);
        $this->info("Desafíos iniciados: {$startedChallenges}");

        // 5. Estadísticas por tipo de desafío
        $challengeTypes = Challenge::select('type')
            ->distinct()
            ->pluck('type')
            ->toArray();

        foreach ($challengeTypes as $type) {
            if (!$type) continue;
            
            $count = DB::table('challenge_user')
                ->join('challenges', 'challenge_user.challenge_id', '=', 'challenges.id')
                ->where('challenges.type', $type)
                ->whereDate('challenge_user.completed_at', $dateStr)
                ->where('challenge_user.status', 'completed')
                ->count();
                
            DailyStatistic::incrementStat($dateStr, 'challenge_completions_by_type', $type, $count);
            $this->info("Desafíos completados (tipo {$type}): {$count}");
        }

        $this->info('Actualización de estadísticas completada.');
        return Command::SUCCESS;
    }
}
