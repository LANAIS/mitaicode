<?php

namespace App\Http\Controllers;

use App\Models\UserAchievement;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAchievementController extends Controller
{
    protected $gamificationService;

    /**
     * Constructor que inyecta el servicio de gamificación.
     *
     * @param GamificationService $gamificationService
     */
    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Muestra todos los logros del usuario autenticado.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $achievements = $this->gamificationService->getUserAchievements(Auth::id());
        $stats = $this->gamificationService->getUserStats(Auth::id());
        
        // Obtener los tipos de logros para agruparlos
        $achievementsByType = $achievements->groupBy(function ($item) {
            if (strpos($item->achievement_type, 'streak_') !== false) {
                return 'streak';
            } elseif (strpos($item->achievement_type, 'challenge_') !== false) {
                return 'challenge';
            } elseif (strpos($item->achievement_type, 'exercise_') !== false) {
                return 'exercise';
            } else {
                return 'other';
            }
        });
        
        return view('gamification.achievements.index', compact('achievements', 'achievementsByType', 'stats'));
    }

    /**
     * Muestra un logro específico.
     *
     * @param int $id ID del logro
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $achievement = UserAchievement::findOrFail($id);
        
        // Verificar que el logro pertenezca al usuario autenticado
        if ($achievement->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permiso para ver este logro.');
        }
        
        return view('gamification.achievements.show', compact('achievement'));
    }
}
