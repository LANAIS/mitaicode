<?php

namespace App\Http\Controllers;

use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
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
     * Muestra las estadísticas del usuario autenticado.
     *
     * @return \Illuminate\Http\Response
     */
    public function userStats()
    {
        $user = Auth::user();
        $stats = $this->gamificationService->getUserStats($user->user_id);
        $achievements = $this->gamificationService->getUserAchievements($user->user_id);
        
        // Obtener los logros más recientes
        $recentAchievements = $achievements->take(5);
        
        return view('gamification.stats', compact('user', 'stats', 'achievements', 'recentAchievements'));
    }

    /**
     * Registra una actividad para el usuario y actualiza sus rachas y puntajes.
     * 
     * Este método se puede llamar desde diferentes partes de la aplicación
     * cuando el usuario completa una acción que debe registrarse para gamificación.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function registerActivity(Request $request)
    {
        $validated = $request->validate([
            'points' => 'sometimes|integer|min:1',
            'completed_challenges' => 'sometimes|integer|min:0',
            'completed_exercises' => 'sometimes|integer|min:0',
        ]);
        
        $points = $validated['points'] ?? 1;
        
        $additionalData = [];
        if (isset($validated['completed_challenges'])) {
            $additionalData['completed_challenges'] = $validated['completed_challenges'];
        }
        
        if (isset($validated['completed_exercises'])) {
            $additionalData['completed_exercises'] = $validated['completed_exercises'];
        }
        
        $success = $this->gamificationService->registerActivity(
            Auth::id(),
            $points,
            $additionalData
        );
        
        if ($request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Actividad registrada correctamente.' : 'Error al registrar la actividad.'
            ]);
        }
        
        if ($success) {
            return back()->with('success', 'Actividad registrada correctamente.');
        } else {
            return back()->with('error', 'Error al registrar la actividad.');
        }
    }
}
