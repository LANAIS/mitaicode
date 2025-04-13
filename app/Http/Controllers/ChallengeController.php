<?php

namespace App\Http\Controllers;

use App\Models\ChallengeProgress;
use App\Models\StudentProfile;
use App\Models\LeaderboardEntry;
use App\Models\Leaderboard;
use App\Models\UserCertificate;
use App\Models\UserAchievement;
use App\Models\UserStreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChallengeController extends Controller
{
    /**
     * Mostrar los desafíos disponibles para el usuario actual.
     *
     * @param string $type Tipo de desafío (python, blocks, ai_prompt)
     * @param string $level Nivel del desafío (principiante, intermedio, avanzado)
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type = 'python', $level = 'principiante')
    {
        $user = Auth::user();
        
        // Obtener el progreso del usuario en este tipo y nivel de desafío
        $progress = ChallengeProgress::where('user_id', $user->user_id)
            ->where('challenge_type', $type)
            ->where('level', $level)
            ->orderBy('challenge_number')
            ->get();
        
        // Obtener certificados obtenidos
        $certificates = UserCertificate::where('user_id', $user->user_id)
            ->where('certificate_type', $type)
            ->get();
        
        // Obtener logros relacionados con este tipo de desafío
        $achievements = UserAchievement::where('user_id', $user->user_id)
            ->where('achievement_type', 'LIKE', $type . '%')
            ->where('is_displayed', true)
            ->orderBy('awarded_at', 'desc')
            ->get();
        
        return response()->json([
            'progress' => $progress,
            'certificates' => $certificates,
            'achievements' => $achievements,
            'completed_count' => $progress->where('is_completed', true)->count(),
            'total_challenges' => 10, // Esto podría venir de una configuración o base de datos
            'current_level' => $level,
        ]);
    }
    
    /**
     * Verificar y actualizar el progreso en un desafío específico.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitCode(Request $request)
    {
        $request->validate([
            'challenge_type' => 'required|string|in:python,blocks,ai_prompt',
            'level' => 'required|string|in:principiante,intermedio,avanzado',
            'challenge_number' => 'required|integer|min:1|max:10',
            'code' => 'required|string',
        ]);
        
        $user = Auth::user();
        $type = $request->challenge_type;
        $level = $request->level;
        $challengeNumber = $request->challenge_number;
        $code = $request->code;
        
        // Buscar o crear un registro de progreso
        $progress = ChallengeProgress::firstOrNew([
            'user_id' => $user->user_id,
            'challenge_type' => $type,
            'level' => $level,
            'challenge_number' => $challengeNumber,
        ]);
        
        // Incrementar el contador de intentos
        $progress->attempts += 1;
        $progress->submitted_code = $code;
        
        // Evaluar el código (en un entorno real, esto sería más complejo)
        $isCorrect = $this->evaluateCode($type, $level, $challengeNumber, $code);
        
        // Si es correcto y no estaba completado antes, actualizar estado
        $newlyCompleted = false;
        if ($isCorrect && !$progress->is_completed) {
            $progress->is_completed = true;
            $progress->completed_at = Carbon::now();
            $newlyCompleted = true;
        }
        
        $progress->save();
        
        // Si se completó por primera vez, verificar si se completó el nivel o se obtuvo un logro
        if ($newlyCompleted) {
            // Contar todos los desafíos completados en este nivel
            $completedCount = ChallengeProgress::where('user_id', $user->user_id)
                ->where('challenge_type', $type)
                ->where('level', $level)
                ->where('is_completed', true)
                ->count();
            
            // Verificar si se completó todo el nivel (asumiendo 10 desafíos por nivel)
            $levelCompleted = false;
            if ($completedCount >= 10) {
                $levelCompleted = true;
                
                // Crear certificado para el nivel
                UserCertificate::create([
                    'user_id' => $user->user_id,
                    'certificate_type' => $type,
                    'level' => $level,
                    'awarded_at' => Carbon::now(),
                ]);
                
                // Crear logro para completar el nivel
                UserAchievement::create([
                    'user_id' => $user->user_id,
                    'achievement_type' => $type . '_level_complete',
                    'description' => 'Completó todos los desafíos de ' . ucfirst($type) . ' - Nivel ' . ucfirst($level),
                    'points' => 100, // Puntos por completar un nivel
                    'icon' => 'trophy',
                    'awarded_at' => Carbon::now(),
                ]);
                
                // Actualizar puntos XP del estudiante
                if ($user->studentProfile) {
                    $levelResult = $user->studentProfile->addXpPoints(100);
                    
                    // Actualizar tabla de clasificación
                    $this->updateLeaderboards($user->user_id, 100, 1);
                }
            }
            
            // Crear logro para el primer desafío completado (si es el primero)
            if ($completedCount == 1) {
                UserAchievement::create([
                    'user_id' => $user->user_id,
                    'achievement_type' => $type . '_first_challenge',
                    'description' => 'Completó su primer desafío de ' . ucfirst($type),
                    'points' => 10,
                    'icon' => 'star',
                    'awarded_at' => Carbon::now(),
                ]);
                
                // Actualizar puntos XP del estudiante
                if ($user->studentProfile) {
                    $user->studentProfile->addXpPoints(10);
                    
                    // Actualizar tabla de clasificación
                    $this->updateLeaderboards($user->user_id, 10, 1);
                }
            }
            
            // Crear logro para 5 desafíos completados
            if ($completedCount == 5) {
                UserAchievement::create([
                    'user_id' => $user->user_id,
                    'achievement_type' => $type . '_five_challenges',
                    'description' => 'Completó 5 desafíos de ' . ucfirst($type),
                    'points' => 50,
                    'icon' => 'medal',
                    'awarded_at' => Carbon::now(),
                ]);
                
                // Actualizar puntos XP del estudiante
                if ($user->studentProfile) {
                    $user->studentProfile->addXpPoints(50);
                    
                    // Actualizar tabla de clasificación
                    $this->updateLeaderboards($user->user_id, 50, 0);
                }
            }
            
            // Para cualquier desafío completado, sumar puntos básicos
            if ($user->studentProfile) {
                // Puntos base por completar un desafío
                $basePoints = 5;
                $user->studentProfile->addXpPoints($basePoints);
                
                // Actualizar tabla de clasificación
                $this->updateLeaderboards($user->user_id, $basePoints, 1);
                
                // Actualizar racha
                $this->updateUserStreak($user->user_id);
            }
        }
        
        return response()->json([
            'success' => $isCorrect,
            'message' => $isCorrect ? '¡Código correcto! Desafío completado.' : 'El código no cumple con los requisitos. Intenta de nuevo.',
            'newly_completed' => $newlyCompleted,
            'level_completed' => $levelCompleted ?? false,
            'next_challenge' => $isCorrect && $challengeNumber < 10 ? $challengeNumber + 1 : null,
        ]);
    }
    
    /**
     * Evaluar si el código cumple con los requisitos del desafío.
     * En un entorno real, esto debería ser mucho más sofisticado.
     *
     * @param string $type
     * @param string $level
     * @param int $challengeNumber
     * @param string $code
     * @return bool
     */
    private function evaluateCode($type, $level, $challengeNumber, $code)
    {
        // Esta es una evaluación muy básica para simular
        // En un entorno real, esto utilizaría sandbox seguro para ejecutar código
        
        if ($type == 'python') {
            switch ($challengeNumber) {
                case 1:
                    return strpos($code, 'print') !== false && strpos($code, '¡Hola Mundo desde Python!') !== false;
                case 2:
                    return strpos($code, '=') !== false && strpos($code, 'print') !== false && preg_match('/\w+\s*=\s*["\'"].*["\'"]/i', $code);
                case 3:
                    return strpos($code, 'print') !== false && (
                        strpos($code, '+') !== false || 
                        strpos($code, '-') !== false || 
                        strpos($code, '*') !== false || 
                        strpos($code, '/') !== false
                    );
                case 4:
                    return strpos($code, 'if') !== false && strpos($code, 'else') !== false;
                case 5:
                    return strpos($code, 'for') !== false || strpos($code, 'while') !== false;
                case 6:
                    return strpos($code, '[') !== false && strpos($code, ']') !== false;
                case 7:
                    return strpos($code, 'def') !== false;
                case 8:
                    return strpos($code, '{') !== false && strpos($code, '}') !== false;
                case 9:
                    return strpos($code, 'try') !== false && strpos($code, 'except') !== false;
                case 10:
                    // Proyecto final: Calculadora - comprobación más compleja
                    return strpos($code, 'def') !== false && 
                        strpos($code, 'input') !== false && 
                        (strpos($code, '+') !== false || strpos($code, '-') !== false);
                default:
                    return false;
            }
        }
        
        // Implementación para otros tipos de desafíos
        return false;
    }
    
    /**
     * Generar y devolver un certificado.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCertificate(Request $request)
    {
        $request->validate([
            'certificate_id' => 'required|integer|exists:user_certificates,id',
        ]);
        
        $certificate = UserCertificate::findOrFail($request->certificate_id);
        
        // Verificar que el certificado pertenezca al usuario actual
        if ($certificate->user_id != Auth::id()) {
            return response()->json(['error' => 'No tienes permiso para acceder a este certificado'], 403);
        }
        
        $user = Auth::user();
        $userData = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name
        ];
        
        return response()->json([
            'certificate' => $certificate,
            'user' => $userData,
            'download_url' => $certificate->certificate_url ?? route('certificates.download', $certificate->id),
        ]);
    }
    
    /**
     * Descargar un certificado.
     *
     * @param int $id ID del certificado
     * @return \Illuminate\Http\Response
     */
    public function downloadCertificate($id)
    {
        $certificate = UserCertificate::findOrFail($id);
        
        // Verificar que el certificado pertenezca al usuario actual
        if ($certificate->user_id != Auth::id()) {
            abort(403, 'No tienes permiso para acceder a este certificado');
        }
        
        // Implementación real: generar un PDF en tiempo real o devolver uno pre-generado
        
        // Simulación para este ejemplo
        return response()->json([
            'message' => 'Función de descarga de certificados en implementación',
            'certificate' => $certificate
        ]);
    }
    
    /**
     * Actualiza las tablas de clasificación con los puntos ganados por completar desafíos.
     *
     * @param int $userId ID del usuario
     * @param int $points Puntos a añadir
     * @param int $challengesCompleted Contador de desafíos completados a incrementar
     * @return void
     */
    private function updateLeaderboards(int $userId, int $points, int $challengesCompleted = 0): void
    {
        // Obtener las tablas de clasificación activas
        $leaderboards = Leaderboard::where('is_active', true)->get();
        
        foreach ($leaderboards as $leaderboard) {
            // Buscar la entrada del usuario en esta tabla o crearla si no existe
            $entry = LeaderboardEntry::firstOrCreate(
                [
                    'leaderboard_id' => $leaderboard->id,
                    'user_id' => $userId
                ],
                [
                    'score' => 0,
                    'streak' => 0,
                    'completed_challenges' => 0,
                    'completed_exercises' => 0,
                    'ranking_position' => 0
                ]
            );
            
            // Añadir los puntos y actualizar el contador de desafíos
            $entry->addPoints($points);
            
            if ($challengesCompleted > 0) {
                $entry->incrementChallenges($challengesCompleted);
            }
        }
    }
    
    /**
     * Actualiza la racha del usuario.
     * 
     * @param int $userId ID del usuario
     * @return void
     */
    private function updateUserStreak(int $userId): void
    {
        // Obtener o crear el registro de racha del usuario
        $streak = UserStreak::firstOrCreate(
            ['user_id' => $userId],
            [
                'last_activity_date' => null,
                'current_streak' => 0,
                'longest_streak' => 0,
                'has_activity_today' => false
            ]
        );
        
        // Registrar la actividad del día
        $streak->registerActivity();
        
        // Actualizar las tablas de clasificación con la nueva racha
        $leaderboards = Leaderboard::where('is_active', true)->get();
        foreach ($leaderboards as $leaderboard) {
            $entry = LeaderboardEntry::where('leaderboard_id', $leaderboard->id)
                ->where('user_id', $userId)
                ->first();
                
            if ($entry) {
                $entry->updateStreak($streak->current_streak);
            }
        }
    }
} 