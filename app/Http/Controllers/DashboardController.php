<?php

namespace App\Http\Controllers;

use App\Models\Leaderboard;
use App\Models\LeaderboardEntry;
use App\Models\User;
use App\Models\UserStreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar la vista del dashboard con leaderboards.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
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
            
        // Si no existen, crearlas
        if (!$weeklyLeaderboard) {
            $weeklyLeaderboard = Leaderboard::createWeekly('Ranking Semanal');
        }
        
        if (!$monthlyLeaderboard) {
            $monthlyLeaderboard = Leaderboard::createMonthly('Ranking Mensual');
        }
        
        // Obtener las mejores entradas para cada tabla
        $weeklyTop = $weeklyLeaderboard->getTopEntries(5);
        $monthlyTop = $monthlyLeaderboard->getTopEntries(5);
        
        // Obtener la posición del usuario actual en cada tabla
        $weeklyUserRank = null;
        $monthlyUserRank = null;
        
        if ($user) {
            $weeklyEntry = LeaderboardEntry::where('leaderboard_id', $weeklyLeaderboard->id)
                ->where('user_id', $user->user_id)
                ->first();
                
            $monthlyEntry = LeaderboardEntry::where('leaderboard_id', $monthlyLeaderboard->id)
                ->where('user_id', $user->user_id)
                ->first();
                
            if ($weeklyEntry) {
                $weeklyUserRank = [
                    'position' => $weeklyEntry->ranking_position,
                    'score' => $weeklyEntry->score,
                    'streak' => $weeklyEntry->streak,
                ];
            }
            
            if ($monthlyEntry) {
                $monthlyUserRank = [
                    'position' => $monthlyEntry->ranking_position,
                    'score' => $monthlyEntry->score,
                    'streak' => $monthlyEntry->streak,
                ];
            }
        }
        
        // Obtener la racha actual del usuario
        $streak = null;
        if ($user) {
            $streak = UserStreak::where('user_id', $user->user_id)->first();
        }
        
        return view('dashboard', compact(
            'weeklyLeaderboard', 
            'monthlyLeaderboard', 
            'weeklyTop', 
            'monthlyTop', 
            'weeklyUserRank', 
            'monthlyUserRank',
            'streak'
        ));
    }
} 