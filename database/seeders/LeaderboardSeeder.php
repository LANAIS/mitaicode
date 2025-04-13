<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Leaderboard;
use Carbon\Carbon;

class LeaderboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear leaderboard semanal
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        Leaderboard::create([
            'name' => 'Ranking Semanal',
            'type' => 'weekly',
            'reference_id' => null,
            'start_date' => $startOfWeek,
            'end_date' => $endOfWeek,
            'is_active' => true,
        ]);

        // Crear leaderboard mensual
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        Leaderboard::create([
            'name' => 'Ranking Mensual',
            'type' => 'monthly',
            'reference_id' => null,
            'start_date' => $startOfMonth,
            'end_date' => $endOfMonth,
            'is_active' => true,
        ]);

        // Crear leaderboard de todos los tiempos
        Leaderboard::create([
            'name' => 'Ranking General',
            'type' => 'all_time',
            'reference_id' => null,
            'start_date' => null,
            'end_date' => null,
            'is_active' => true,
        ]);

        $this->command->info('Se han creado los leaderboards iniciales.');
    }
}
