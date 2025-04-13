<?php

namespace App\Http\Controllers;

use App\Models\Leaderboard;
use App\Models\LeaderboardEntry;
use App\Models\User;
use App\Models\UserStreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    /**
     * Mostrar la página principal de tablas de puntajes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtener tablas de puntajes activas
        $weeklyLeaderboard = Leaderboard::where('type', 'weekly')
            ->where('is_active', true)
            ->where('reference_id', null)
            ->orderBy('created_at', 'desc')
            ->first();
            
        $monthlyLeaderboard = Leaderboard::where('type', 'monthly')
            ->where('is_active', true)
            ->where('reference_id', null)
            ->orderBy('created_at', 'desc')
            ->first();
            
        $allTimeLeaderboard = Leaderboard::where('type', 'all_time')
            ->where('is_active', true)
            ->where('reference_id', null)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Si no existen, crearlas
        if (!$weeklyLeaderboard) {
            $weeklyLeaderboard = Leaderboard::createWeekly('Ranking Semanal');
        }
        
        if (!$monthlyLeaderboard) {
            $monthlyLeaderboard = Leaderboard::createMonthly('Ranking Mensual');
        }
        
        if (!$allTimeLeaderboard) {
            $allTimeLeaderboard = Leaderboard::create([
                'title' => 'Ranking General',
                'type' => 'all_time',
                'is_active' => true,
            ]);
        }
        
        // Obtener las mejores entradas para cada tabla
        $weeklyTop = $weeklyLeaderboard->getTopEntries(10);
        $monthlyTop = $monthlyLeaderboard->getTopEntries(10);
        $allTimeTop = $allTimeLeaderboard->getTopEntries(10);
        
        // Obtener la posición del usuario actual en cada tabla
        $userPositions = [
            'weekly' => $this->getUserPosition($weeklyLeaderboard->id, Auth::id()),
            'monthly' => $this->getUserPosition($monthlyLeaderboard->id, Auth::id()),
            'all_time' => $this->getUserPosition($allTimeLeaderboard->id, Auth::id()),
        ];
        
        return view('gamification.leaderboards.index', compact(
            'weeklyTop', 
            'monthlyTop', 
            'allTimeTop', 
            'userPositions',
            'weeklyLeaderboard',
            'monthlyLeaderboard',
            'allTimeLeaderboard'
        ));
    }
    
    /**
     * Mostrar una tabla de puntajes específica.
     *
     * @param int $id ID de la tabla de puntajes
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leaderboard = Leaderboard::findOrFail($id);
        $entries = $leaderboard->entries()
            ->orderBy('score', 'desc')
            ->with('user')
            ->paginate(25);
            
        $userPosition = $this->getUserPosition($leaderboard->id, Auth::id());
        
        return view('gamification.leaderboards.show', compact('leaderboard', 'entries', 'userPosition'));
    }
    
    /**
     * Obtener la posición de un usuario en una tabla de puntajes.
     *
     * @param int $leaderboardId ID de la tabla de puntajes
     * @param int $userId ID del usuario
     * @return array|null Información de la posición del usuario o null si no está en la tabla
     */
    private function getUserPosition($leaderboardId, $userId)
    {
        $entry = LeaderboardEntry::where('leaderboard_id', $leaderboardId)
            ->where('user_id', $userId)
            ->first();
            
        if (!$entry) {
            return null;
        }
        
        return [
            'position' => $entry->ranking_position,
            'score' => $entry->score,
            'streak' => $entry->streak,
            'completed_challenges' => $entry->completed_challenges,
            'completed_exercises' => $entry->completed_exercises,
        ];
    }
    
    /**
     * Registrar actividad del usuario y actualizar tablas de puntajes.
     * 
     * @param int $userId ID del usuario
     * @param int $points Puntos ganados
     * @param array $additionalData Datos adicionales (completed_challenges, completed_exercises)
     * @return bool
     */
    public function registerUserActivity($userId, $points = 1, $additionalData = [])
    {
        try {
            DB::beginTransaction();
            
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
            
            $streak->registerActivity();
            
            // 2. Añadir puntos al usuario en todas las tablas activas
            $weeklyLeaderboard = Leaderboard::where('type', 'weekly')
                ->where('is_active', true)
                ->where('reference_id', null)
                ->orderBy('created_at', 'desc')
                ->first();
                
            $monthlyLeaderboard = Leaderboard::where('type', 'monthly')
                ->where('is_active', true)
                ->where('reference_id', null)
                ->orderBy('created_at', 'desc')
                ->first();
                
            $allTimeLeaderboard = Leaderboard::where('type', 'all_time')
                ->where('is_active', true)
                ->where('reference_id', null)
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Datos para actualizar
            $updateData = [
                'streak' => $streak->current_streak
            ];
            
            // Añadir datos adicionales si están presentes
            if (isset($additionalData['completed_challenges'])) {
                $updateData['completed_challenges'] = DB::raw('completed_challenges + ' . (int)$additionalData['completed_challenges']);
            }
            
            if (isset($additionalData['completed_exercises'])) {
                $updateData['completed_exercises'] = DB::raw('completed_exercises + ' . (int)$additionalData['completed_exercises']);
            }
            
            // Actualizar o crear entradas en cada leaderboard
            if ($weeklyLeaderboard) {
                $this->updateLeaderboardEntry($weeklyLeaderboard->id, $userId, $points, $updateData);
            }
            
            if ($monthlyLeaderboard) {
                $this->updateLeaderboardEntry($monthlyLeaderboard->id, $userId, $points, $updateData);
            }
            
            if ($allTimeLeaderboard) {
                $this->updateLeaderboardEntry($allTimeLeaderboard->id, $userId, $points, $updateData);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar actividad: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar o crear una entrada en la tabla de puntajes.
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
            $entry->points = 0;
            $entry->streak = $updateData['streak'] ?? 0;
            $entry->completed_challenges = 0;
            $entry->completed_exercises = 0;
            $entry->save();
        }
        
        // Actualizar puntaje
        $entry->points += $points;
        
        // Actualizar otros campos
        if (isset($updateData['streak'])) {
            $entry->streak = $updateData['streak'];
        }
        
        if (isset($updateData['completed_challenges'])) {
            if (is_string($updateData['completed_challenges']) && strpos($updateData['completed_challenges'], 'completed_challenges + ') !== false) {
                $value = (int) str_replace('completed_challenges + ', '', $updateData['completed_challenges']);
                $entry->completed_challenges += $value;
            } else {
                $entry->completed_challenges = $updateData['completed_challenges'];
            }
        }
        
        if (isset($updateData['completed_exercises'])) {
            if (is_string($updateData['completed_exercises']) && strpos($updateData['completed_exercises'], 'completed_exercises + ') !== false) {
                $value = (int) str_replace('completed_exercises + ', '', $updateData['completed_exercises']);
                $entry->completed_exercises += $value;
            } else {
                $entry->completed_exercises = $updateData['completed_exercises'];
            }
        }
        
        $entry->save();
        
        // Actualizar posiciones del ranking
        $leaderboard = Leaderboard::find($leaderboardId);
        if ($leaderboard) {
            $leaderboard->updateRankings();
        }
        
        return $entry;
    }
}
