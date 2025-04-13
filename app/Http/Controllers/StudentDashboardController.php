<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    /**
     * Muestra el dashboard del estudiante
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Verificar que el usuario está autenticado y es un estudiante
        if (!Auth::check() || Auth::user()->role !== 'student') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta página');
        }
        
        // Obtenemos el perfil del estudiante
        $student = Auth::user();
        $studentProfile = $student->studentProfile;
        
        // Obtener la racha de actividad del usuario
        $streak = \App\Models\UserStreak::where('user_id', $student->user_id)->first();
        
        // Contar desafíos completados
        $completedChallenges = \App\Models\ChallengeProgress::where('user_id', $student->user_id)
            ->where('is_completed', true)
            ->count();
        
        // Asegurarse de que el perfil del estudiante tenga los puntos XP actualizados
        if ($studentProfile) {
            // Forzar recarga desde la base de datos
            $studentProfile->refresh();
        }
        
        // Podemos agregar más datos según necesitemos
        
        return view('students.dashboard', [
            'student' => $student,
            'studentProfile' => $studentProfile,
            'streak' => $streak,
            'completedChallenges' => $completedChallenges
        ]);
    }
}
