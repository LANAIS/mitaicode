<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStreak;
use App\Models\Leaderboard;
use App\Models\LeaderboardEntry;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GamificationService
{
    /**
     * Registra una actividad del usuario y actualiza sus rachas y puntajes.
     *
     * @param int $userId ID del usuario
     * @param int $points Puntos a añadir por la actividad
     * @param array $additionalData Datos adicionales para actualizar (completed_challenges, completed_exercises)
     * @return bool
     */
    public function registerActivity(int $userId, int $points = 1, array $additionalData = []): bool
    {
        try {
            DB::beginTransaction();
            
            // Log adicional para depuración
            Log::info("GamificationService::registerActivity - Usuario: {$userId}, Puntos: {$points}");
            
            // 1. Actualizar racha del usuario
            $streak = UserStreak::firstOrCreate(
                ['user_id' => $userId],
                [
                    'last_activity_date' => Carbon::today()->subDay(),
                    'current_streak' => 0,
                    'longest_streak' => 0,
                    'has_activity_today' => false
                ]
            );
            
            $wasUpdated = $streak->registerActivity();
            
            // 2. Verificar y otorgar logros relacionados con rachas
            if ($wasUpdated) {
                $this->checkStreakAchievements($userId, $streak->current_streak);
            }
            
            // 3. Añadir puntos al usuario en todas las tablas activas
            $weeklyLeaderboard = $this->getOrCreateLeaderboard('weekly', 'Ranking Semanal');
            $monthlyLeaderboard = $this->getOrCreateLeaderboard('monthly', 'Ranking Mensual');
            $allTimeLeaderboard = $this->getOrCreateLeaderboard('all_time', 'Ranking General');
            
            // Datos para actualizar
            $updateData = [
                'streak' => $streak->current_streak
            ];
            
            // Procesar datos adicionales para asegurar que son valores simples, no expresiones
            if (isset($additionalData['completed_challenges'])) {
                $challengesValue = (int) $additionalData['completed_challenges'];
                Log::info("Registrando {$challengesValue} desafíos completados");
                $updateData['completed_challenges'] = $challengesValue;
                
                // Verificar logros por desafíos completados
                if ($challengesValue > 0) {
                    $this->checkChallengeAchievements($userId);
                }
            }
            
            if (isset($additionalData['completed_exercises'])) {
                $exercisesValue = (int) $additionalData['completed_exercises'];
                Log::info("Registrando {$exercisesValue} ejercicios completados");
                $updateData['completed_exercises'] = $exercisesValue;
                
                // Verificar logros por ejercicios completados
                if ($exercisesValue > 0) {
                    $this->checkExerciseAchievements($userId);
                }
            }
            
            // Actualizar o crear entradas en cada leaderboard
            $this->updateLeaderboardEntry($weeklyLeaderboard->id, $userId, $points, $updateData);
            $this->updateLeaderboardEntry($monthlyLeaderboard->id, $userId, $points, $updateData);
            $this->updateLeaderboardEntry($allTimeLeaderboard->id, $userId, $points, $updateData);
            
            DB::commit();
            Log::info("Actividad registrada exitosamente para el usuario {$userId}");
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar actividad en GamificationService: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene o crea una tabla de puntajes si no existe.
     *
     * @param string $type Tipo de tabla (weekly, monthly, all_time)
     * @param string $name Nombre de la tabla
     * @return Leaderboard
     */
    private function getOrCreateLeaderboard(string $type, string $name): Leaderboard
    {
        $leaderboard = Leaderboard::where('type', $type)
            ->where('is_active', true)
            ->where('reference_id', null)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$leaderboard) {
            if ($type === 'weekly') {
                $leaderboard = Leaderboard::createWeekly($name);
            } elseif ($type === 'monthly') {
                $leaderboard = Leaderboard::createMonthly($name);
            } else {
                $leaderboard = Leaderboard::create([
                    'name' => $name,
                    'type' => $type,
                    'is_active' => true,
                ]);
            }
        }
        
        return $leaderboard;
    }
    
    /**
     * Actualiza o crea una entrada en la tabla de puntajes.
     *
     * @param int $leaderboardId ID de la tabla
     * @param int $userId ID del usuario
     * @param int $points Puntos a añadir
     * @param array $updateData Datos adicionales para actualizar
     * @return LeaderboardEntry
     */
    private function updateLeaderboardEntry($leaderboardId, $userId, $points, $updateData = [])
    {
        $entry = LeaderboardEntry::firstOrNew([
            'leaderboard_id' => $leaderboardId,
            'user_id' => $userId
        ]);
        
        // Si es nueva, establecer valores iniciales
        if (!$entry->exists) {
            $entry->score = 0;
            $entry->streak = $updateData['streak'] ?? 0;
            $entry->completed_challenges = 0;
            $entry->completed_exercises = 0;
            $entry->save();
        }
        
        // Actualizar puntaje
        $entry->score += $points;
        
        // Actualizar otros campos
        if (isset($updateData['streak'])) {
            $entry->streak = $updateData['streak'];
        }
        
        if (isset($updateData['completed_challenges'])) {
            // Incrementar el contador con el valor proporcionado
            $entry->completed_challenges += (int)$updateData['completed_challenges'];
        }
        
        if (isset($updateData['completed_exercises'])) {
            // Incrementar el contador con el valor proporcionado
            $entry->completed_exercises += (int)$updateData['completed_exercises'];
        }
        
        $entry->save();
        
        try {
            // Actualizar posiciones en el ranking
            $leaderboard = Leaderboard::find($leaderboardId);
            if ($leaderboard) {
                $leaderboard->updateRankings();
            }
        } catch (\Exception $e) {
            Log::warning('No se pudieron actualizar los rankings: ' . $e->getMessage());
            // Continuamos sin interrumpir la ejecución
        }
        
        return $entry;
    }
    
    /**
     * Verifica si el usuario ha alcanzado logros relacionados con rachas.
     *
     * @param int $userId ID del usuario
     * @param int $currentStreak Racha actual
     * @return void
     */
    private function checkStreakAchievements(int $userId, int $currentStreak): void
    {
        $achievements = [
            3 => [
                'type' => 'streak_3_days',
                'description' => '¡3 días seguidos activo!',
                'points' => 10,
                'icon' => 'streak-3'
            ],
            7 => [
                'type' => 'streak_7_days',
                'description' => '¡Una semana completa de actividad!',
                'points' => 25,
                'icon' => 'streak-7'
            ],
            14 => [
                'type' => 'streak_14_days',
                'description' => '¡Dos semanas seguidas activo!',
                'points' => 50,
                'icon' => 'streak-14'
            ],
            30 => [
                'type' => 'streak_30_days',
                'description' => '¡Un mes completo de actividad!',
                'points' => 100,
                'icon' => 'streak-30'
            ],
        ];
        
        if (isset($achievements[$currentStreak])) {
            $achievement = $achievements[$currentStreak];
            $this->awardAchievement(
                $userId, 
                $achievement['type'], 
                $achievement['description'], 
                $achievement['points'],
                $achievement['icon']
            );
        }
    }
    
    /**
     * Verifica logros relacionados con desafíos completados.
     *
     * @param int $userId ID del usuario
     * @return void
     */
    private function checkChallengeAchievements(int $userId): void
    {
        // Obtener el número total de desafíos completados por el usuario
        $completedChallenges = LeaderboardEntry::where('user_id', $userId)
            ->where('leaderboard_id', function ($query) {
                $query->select('id')
                    ->from('leaderboards')
                    ->where('type', 'all_time')
                    ->first();
            })
            ->value('completed_challenges') ?? 0;
        
        $achievements = [
            1 => [
                'type' => 'challenge_1',
                'description' => '¡Primer desafío completado!',
                'points' => 10,
                'icon' => 'challenge-1'
            ],
            5 => [
                'type' => 'challenge_5',
                'description' => '5 desafíos completados',
                'points' => 25,
                'icon' => 'challenge-5'
            ],
            10 => [
                'type' => 'challenge_10',
                'description' => '10 desafíos completados',
                'points' => 50,
                'icon' => 'challenge-10'
            ],
            25 => [
                'type' => 'challenge_25',
                'description' => '25 desafíos completados',
                'points' => 100,
                'icon' => 'challenge-25'
            ],
            50 => [
                'type' => 'challenge_50',
                'description' => '50 desafíos completados - ¡Experto!',
                'points' => 200,
                'icon' => 'challenge-50'
            ],
        ];
        
        foreach ($achievements as $threshold => $achievement) {
            if ($completedChallenges >= $threshold) {
                $this->awardAchievement(
                    $userId, 
                    $achievement['type'], 
                    $achievement['description'], 
                    $achievement['points'],
                    $achievement['icon']
                );
            }
        }
    }
    
    /**
     * Verifica logros relacionados con ejercicios completados.
     *
     * @param int $userId ID del usuario
     * @return void
     */
    private function checkExerciseAchievements(int $userId): void
    {
        // Obtener el número total de ejercicios completados por el usuario
        $completedExercises = LeaderboardEntry::where('user_id', $userId)
            ->where('leaderboard_id', function ($query) {
                $query->select('id')
                    ->from('leaderboards')
                    ->where('type', 'all_time')
                    ->first();
            })
            ->value('completed_exercises') ?? 0;
        
        $achievements = [
            10 => [
                'type' => 'exercise_10',
                'description' => '10 ejercicios completados',
                'points' => 15,
                'icon' => 'exercise-10'
            ],
            25 => [
                'type' => 'exercise_25',
                'description' => '25 ejercicios completados',
                'points' => 30,
                'icon' => 'exercise-25'
            ],
            50 => [
                'type' => 'exercise_50',
                'description' => '50 ejercicios completados',
                'points' => 75,
                'icon' => 'exercise-50'
            ],
            100 => [
                'type' => 'exercise_100',
                'description' => '100 ejercicios completados - ¡Maestro!',
                'points' => 150,
                'icon' => 'exercise-100'
            ],
        ];
        
        foreach ($achievements as $threshold => $achievement) {
            if ($completedExercises >= $threshold) {
                $this->awardAchievement(
                    $userId, 
                    $achievement['type'], 
                    $achievement['description'], 
                    $achievement['points'],
                    $achievement['icon']
                );
            }
        }
    }
    
    /**
     * Otorga un logro al usuario si aún no lo tiene.
     *
     * @param int $userId ID del usuario
     * @param string $type Tipo de logro
     * @param string $description Descripción del logro
     * @param int $points Puntos otorgados
     * @param string|null $icon Icono del logro
     * @return bool
     */
    public function awardAchievement(int $userId, string $type, string $description, int $points, ?string $icon = null): bool
    {
        // Verificar si el usuario ya tiene este logro
        $existing = UserAchievement::where('user_id', $userId)
            ->where('achievement_type', $type)
            ->exists();
            
        if ($existing) {
            return false;
        }
        
        // Otorgar el nuevo logro
        UserAchievement::create([
            'user_id' => $userId,
            'achievement_type' => $type,
            'description' => $description,
            'points' => $points,
            'icon' => $icon,
            'awarded_at' => now(),
        ]);
        
        // Añadir puntos al leaderboard general
        $allTimeLeaderboard = $this->getOrCreateLeaderboard('all_time', 'Ranking General');
        $this->updateLeaderboardEntry($allTimeLeaderboard->id, $userId, $points, []);
        
        return true;
    }
    
    /**
     * Procesa las rachas de todos los usuarios para reiniciar aquellas inactivas.
     * Este método debe ejecutarse diariamente mediante un job programado.
     *
     * @return void
     */
    public function processStreaks(): void
    {
        UserStreak::processStreaks();
    }
    
    /**
     * Crea nuevas tablas de puntajes semanales y mensuales cuando sea necesario.
     * Este método debe ejecutarse mediante un job programado.
     *
     * @return void
     */
    public function rotateLeaderboards(): void
    {
        $now = Carbon::now();
        
        // Verificar si es necesario crear una nueva tabla semanal
        $latestWeekly = Leaderboard::where('type', 'weekly')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($latestWeekly && $now->greaterThan($latestWeekly->end_date)) {
            // Desactivar la tabla actual
            $latestWeekly->is_active = false;
            $latestWeekly->save();
            
            // Crear una nueva tabla semanal
            Leaderboard::createWeekly('Ranking Semanal');
        }
        
        // Verificar si es necesario crear una nueva tabla mensual
        $latestMonthly = Leaderboard::where('type', 'monthly')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($latestMonthly && $now->greaterThan($latestMonthly->end_date)) {
            // Desactivar la tabla actual
            $latestMonthly->is_active = false;
            $latestMonthly->save();
            
            // Crear una nueva tabla mensual
            Leaderboard::createMonthly('Ranking Mensual');
        }
    }
    
    /**
     * Obtiene los logros de un usuario.
     *
     * @param int $userId ID del usuario
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserAchievements(int $userId)
    {
        return UserAchievement::where('user_id', $userId)
            ->orderBy('awarded_at', 'desc')
            ->get();
    }
    
    /**
     * Obtiene el resumen de estadísticas de un usuario.
     *
     * @param int $userId ID del usuario
     * @return array
     */
    public function getUserStats(int $userId): array
    {
        // Obtener la racha actual
        $streak = UserStreak::where('user_id', $userId)->first();
        
        // Obtener posiciones en las tablas de puntajes
        $weeklyLeaderboard = $this->getOrCreateLeaderboard('weekly', 'Ranking Semanal');
        $monthlyLeaderboard = $this->getOrCreateLeaderboard('monthly', 'Ranking Mensual');
        $allTimeLeaderboard = $this->getOrCreateLeaderboard('all_time', 'Ranking General');
        
        $weeklyEntry = LeaderboardEntry::where('leaderboard_id', $weeklyLeaderboard->id)
            ->where('user_id', $userId)
            ->first();
            
        $monthlyEntry = LeaderboardEntry::where('leaderboard_id', $monthlyLeaderboard->id)
            ->where('user_id', $userId)
            ->first();
            
        $allTimeEntry = LeaderboardEntry::where('leaderboard_id', $allTimeLeaderboard->id)
            ->where('user_id', $userId)
            ->first();
        
        // Contar logros
        $achievementsCount = UserAchievement::where('user_id', $userId)->count();
        $achievementsPoints = UserAchievement::where('user_id', $userId)->sum('points');
        
        return [
            'streak' => $streak ? $streak->current_streak : 0,
            'longest_streak' => $streak ? $streak->longest_streak : 0,
            'weekly_position' => $weeklyEntry ? $weeklyEntry->ranking_position : null,
            'weekly_score' => $weeklyEntry ? $weeklyEntry->score : 0,
            'monthly_position' => $monthlyEntry ? $monthlyEntry->ranking_position : null,
            'monthly_score' => $monthlyEntry ? $monthlyEntry->score : 0,
            'all_time_position' => $allTimeEntry ? $allTimeEntry->ranking_position : null,
            'all_time_score' => $allTimeEntry ? $allTimeEntry->score : 0,
            'completed_challenges' => $allTimeEntry ? $allTimeEntry->completed_challenges : 0,
            'completed_exercises' => $allTimeEntry ? $allTimeEntry->completed_exercises : 0,
            'achievements_count' => $achievementsCount,
            'achievements_points' => $achievementsPoints,
        ];
    }
} 