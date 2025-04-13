<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Challenge;
use App\Models\Hackathon;
use App\Models\StoreItem;
use App\Models\UserInventory;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AnalyticsController extends Controller
{
    /**
     * Mostrar el dashboard de analíticas
     */
    public function index()
    {
        // Estadísticas de usuarios
        $totalUsers = User::count();
        $totalStudents = StudentProfile::count();
        $totalTeachers = TeacherProfile::count();
        
        // Usuarios nuevos en los últimos 30 días
        $newUsersLast30Days = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        
        // Usuarios activos
        $activeUsersLast30Days = User::where('last_login_at', '>=', Carbon::now()->subDays(30))->count();
        
        // Desafíos y hackathones
        $totalChallenges = Challenge::count();
        $totalHackathons = Hackathon::count();
        
        // Participaciones
        $challengeParticipations = DB::table('challenge_user')->distinct('user_id')->count('user_id');
        $hackathonParticipations = DB::table('hackathon_team_members')->distinct('user_id')->count('user_id');
        
        // Transacciones en tienda
        $totalTransactions = UserInventory::count();

        // Verificar si la columna 'purchases' está disponible
        $totalItemsSold = 0;
        try {
            // Intentar usar la columna purchases
            $totalItemsSold = StoreItem::sum('purchases');
            if ($totalItemsSold == 0) {
                // Si es cero, quizá la columna existe pero no tiene datos
                $totalItemsSold = UserInventory::count();
            }
        } catch (\Exception $e) {
            // Si hay error, usar el conteo de inventario
            $totalItemsSold = UserInventory::count();
            // Registrar error en log
            \Illuminate\Support\Facades\Log::error('Error al obtener suma de purchases: ' . $e->getMessage());
        }
        
        // Datos para gráficos
        $userRegistrationData = $this->getUserRegistrationTrend();
        $userActivityData = $this->getUserActivityTrend();
        $challengeCompletionData = $this->getChallengeCompletionTrend();
        
        return view('admin.analytics.index', compact(
            'totalUsers',
            'totalStudents',
            'totalTeachers',
            'newUsersLast30Days',
            'activeUsersLast30Days',
            'totalChallenges',
            'totalHackathons',
            'challengeParticipations',
            'hackathonParticipations',
            'totalTransactions',
            'totalItemsSold',
            'userRegistrationData',
            'userActivityData',
            'challengeCompletionData'
        ));
    }
    
    /**
     * Mostrar estadísticas de usuarios
     */
    public function users()
    {
        // Por role (obtenemos directamente desde la tabla users)
        $usersByRole = [
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'admins' => User::where('role', 'admin')->count()
        ];
        
        // Calcular total de usuarios
        $totalUsers = User::count();
        
        // Verificamos si hay discrepancia
        $sumByRoles = array_sum($usersByRole);
        if ($sumByRoles < $totalUsers) {
            // Hay usuarios sin rol asignado o con otros roles
            $usersByRole['otros'] = $totalUsers - $sumByRoles;
        }
        
        // Por nivel (estudiantes)
        $usersByLevel = StudentProfile::select('level', DB::raw('count(*) as total'))
            ->groupBy('level')
            ->orderBy('level')
            ->pluck('total', 'level')
            ->toArray();
        
        // Por fecha de registro (últimos 12 meses)
        $usersByMonth = $this->getUsersByMonth();
        
        // Por actividad
        $userActivity = [
            'active_today' => $this->getActiveUsers(1),
            'active_7days' => $this->getActiveUsers(7),
            'active_30days' => $this->getActiveUsers(30),
            'inactive_30days' => User::where('last_login_at', '<', Carbon::now()->subDays(30))->orWhereNull('last_login_at')->count()
        ];
        
        // Tasa de retención
        $retentionRate = $this->calculateRetentionRate();
        
        // Tiempo promedio de sesión
        $avgSessionTime = $this->getAverageSessionTime();
        
        // Registros recientes para debug
        $recentUsers = User::select('user_id', 'username', 'email', 'role', 'created_at', 'last_login_at')
                       ->orderBy('created_at', 'desc')
                       ->take(5)
                       ->get();
        
        return view('admin.analytics.users', compact(
            'usersByRole',
            'usersByLevel',
            'usersByMonth',
            'userActivity',
            'retentionRate',
            'avgSessionTime',
            'totalUsers',
            'recentUsers'
        ));
    }
    
    /**
     * Mostrar estadísticas de contenido/engagement
     */
    public function content(Request $request)
    {
        // Verificar si se solicitó actualizar las estadísticas
        $refresh = $request->has('refresh');
        
        // Si se solicitó refrescar, actualizar las estadísticas diarias
        if ($refresh) {
            // Usar el modelo DailyStatistic para actualizar las estadísticas de hoy
            $today = Carbon::today()->format('Y-m-d');
            
            // Actualizar estadísticas de desafíos completados
            $completedChallenges = DB::table('challenge_user')
                ->whereDate('completed_at', $today)
                ->where('status', 'completed')
                ->count();
            \App\Models\DailyStatistic::incrementStat($today, 'challenge_completions', null, $completedChallenges);
            
            // Actualizar estadísticas de desafíos en progreso
            $inProgressChallenges = DB::table('challenge_user')
                ->where('status', 'in_progress')
                ->count();
            \App\Models\DailyStatistic::incrementStat($today, 'challenges_in_progress', null, $inProgressChallenges);
            
            // Actualizar contadores de compras por categoría 
            try {
                // Intentar actualizar el conteo de compras en StoreItem
                $purchaseCounts = UserInventory::select('item_id', DB::raw('count(*) as count'))
                    ->groupBy('item_id')
                    ->get();
                    
                foreach ($purchaseCounts as $purchase) {
                    DB::update(
                        'UPDATE store_items SET purchases = ? WHERE item_id = ?',
                        [$purchase->count, $purchase->item_id]
                    );
                }
                
                // Registrar éxito en log
                \Illuminate\Support\Facades\Log::info('Contadores de compras actualizados exitosamente');
            } catch (\Exception $e) {
                // Registrar error en log
                \Illuminate\Support\Facades\Log::error('Error al actualizar contadores de compras: ' . $e->getMessage());
            }
            
            // Notificar al usuario que los datos se han actualizado
            session()->flash('success', 'Los datos analíticos se han actualizado correctamente.');
        }
        
        // Desafíos y participación
        $totalChallenges = Challenge::count();
        $completedChallenges = DB::table('challenge_user')->where('status', 'completed')->count();
        $inProgressChallenges = DB::table('challenge_user')->where('status', 'in_progress')->count();
        
        $challengeStats = [
            'total' => $totalChallenges,
            'completed' => $completedChallenges,
            'in_progress' => $inProgressChallenges,
            'avg_completion_rate' => $this->getAverageChallengeCompletionRate(),
            'start_rate' => $totalChallenges > 0 ? 
                round((($completedChallenges + $inProgressChallenges) / $totalChallenges) * 100, 1) : 0
        ];
        
        // Hackathones y participación
        $hackathonStats = [
            'total' => Hackathon::count(),
            'active' => Hackathon::where('status', 'active')->count(),
            'completed' => Hackathon::where('status', 'completed')->count(),
            'avg_participants' => $this->getAverageHackathonParticipants()
        ];
        
        // Verificar si la columna compras está disponible
        $totalItemsSold = 0;
        try {
            // Intentar usar la columna purchases
            $totalItemsSold = StoreItem::sum('purchases');
            if ($totalItemsSold == 0) {
                // Si es cero, quizá la columna existe pero no tiene datos
                $totalItemsSold = UserInventory::count();
            }
        } catch (\Exception $e) {
            // Si hay error, usar el conteo de inventario
            $totalItemsSold = UserInventory::count();
        }
        
        // Actividad en la tienda
        $storeStats = [
            'total_items' => StoreItem::count(),
            'total_purchases' => UserInventory::count(),
            'most_popular_category' => $this->getMostPopularStoreCategory(),
            'revenue' => UserInventory::join('store_items', 'user_inventories.item_id', '=', 'store_items.item_id')
                ->sum(DB::raw('store_items.price'))
        ];
        
        return view('admin.analytics.content', compact(
            'challengeStats',
            'hackathonStats',
            'storeStats'
        ));
    }
    
    /**
     * Exportar datos de analítica
     */
    public function export(Request $request)
    {
        $dataType = $request->input('data_type', 'users');
        $dateRange = $request->input('date_range', '30');
        
        // Lógica para exportar los datos
        // Por ahora retornamos a la vista principal
        return redirect()->route('admin.analytics.index')
            ->with('info', 'La funcionalidad de exportación estará disponible próximamente');
    }
    
    /**
     * Obtener usuarios activos en un período dado
     */
    private function getActiveUsers($days)
    {
        return User::where('last_login_at', '>=', Carbon::now()->subDays($days))->count();
    }
    
    /**
     * Calcular tasa de retención
     */
    private function calculateRetentionRate()
    {
        // Usuarios que se registraron hace 30 días o más
        $usersRegistered30DaysAgo = User::where('created_at', '<=', Carbon::now()->subDays(30))->count();
        
        if ($usersRegistered30DaysAgo === 0) {
            return 0;
        }
        
        // Usuarios que se registraron hace 30 días o más y han estado activos en los últimos 7 días
        $activeUsers = User::where('created_at', '<=', Carbon::now()->subDays(30))
            ->where('last_login_at', '>=', Carbon::now()->subDays(7))
            ->count();
        
        return round(($activeUsers / $usersRegistered30DaysAgo) * 100, 2);
    }
    
    /**
     * Obtener tiempo promedio de sesión
     */
    private function getAverageSessionTime()
    {
        // En una aplicación real, necesitarías datos de inicio y fin de sesión
        // Aquí simulamos un valor promedio en minutos
        return 25;
    }
    
    /**
     * Obtener tasa promedio de finalización de desafíos
     */
    private function getAverageChallengeCompletionRate()
    {
        $totalParticipations = DB::table('challenge_user')->count();
        
        if ($totalParticipations === 0) {
            return 0;
        }
        
        $completedChallenges = DB::table('challenge_user')
            ->where('status', 'completed')
            ->count();
        
        // Si no hay completados, devolver 0 para evitar división por cero
        if ($completedChallenges === 0) {
            return 0;
        }
        
        // Calcular tasa de finalización
        return round(($completedChallenges / $totalParticipations) * 100, 1);
    }
    
    /**
     * Obtener promedio de participantes por hackathon
     */
    private function getAverageHackathonParticipants()
    {
        $totalHackathons = Hackathon::count();
        
        if ($totalHackathons === 0) {
            return 0;
        }
        
        $totalParticipants = DB::table('hackathon_team_members')->count();
        
        return round($totalParticipants / $totalHackathons, 2);
    }
    
    /**
     * Obtener categoría más popular de la tienda
     */
    private function getMostPopularStoreCategory()
    {
        $result = UserInventory::join('store_items', 'user_inventories.item_id', '=', 'store_items.item_id')
            ->select('store_items.category', DB::raw('count(*) as total'))
            ->groupBy('store_items.category')
            ->orderByDesc('total')
            ->first();
        
        return $result ? $result->category : null;
    }
    
    /**
     * Obtener tendencia de registro de usuarios
     */
    private function getUserRegistrationTrend()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        // Crear un periodo de días
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        
        $userRegistrationData = [];
        
        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            
            $count = User::whereDate('created_at', $day)->count();
            
            $userRegistrationData[] = [
                'date' => $day,
                'count' => $count
            ];
        }
        
        return $userRegistrationData;
    }
    
    /**
     * Obtener tendencia de actividad de usuarios
     */
    private function getUserActivityTrend()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        // Crear un periodo de días
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        
        $userActivityData = [];
        
        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            
            $count = User::whereDate('last_login_at', $day)->count();
            
            $userActivityData[] = [
                'date' => $day,
                'count' => $count
            ];
        }
        
        return $userActivityData;
    }
    
    /**
     * Obtener tendencia de finalización de desafíos
     */
    private function getChallengeCompletionTrend()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        // Crear un periodo de días
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        
        $challengeCompletionData = [];
        
        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            
            // Intentar obtener estadísticas de la nueva tabla daily_statistics
            $stat = \App\Models\DailyStatistic::where('date', $day)
                ->where('metric_type', 'challenge_completions')
                ->first();
                
            if ($stat) {
                $count = $stat->count;
            } else {
                // Si no hay estadísticas, usar consulta tradicional con medidas de seguridad
                try {
                    if (Schema::hasColumn('challenge_user', 'status')) {
                        $count = DB::table('challenge_user')
                            ->whereDate('completed_at', $day)
                            ->where('status', 'completed')
                            ->count();
                    } else {
                        $count = DB::table('challenge_user')
                            ->whereDate('completed_at', $day)
                            ->count();
                    }
                } catch (\Exception $e) {
                    $count = 0;
                }
            }
            
            $challengeCompletionData[] = [
                'date' => $day,
                'count' => $count
            ];
        }
        
        return $challengeCompletionData;
    }
    
    /**
     * Obtener usuarios por mes (últimos 12 meses)
     */
    private function getUsersByMonth()
    {
        $result = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthName = $startDate->locale('es')->monthName;
            
            $count = User::whereBetween('created_at', [$startDate, $endDate])->count();
            
            $result[] = [
                'month' => $monthName,
                'count' => $count
            ];
        }
        
        return $result;
    }
    
    /**
     * Refrescar todas las estadísticas
     */
    public function refreshAll(Request $request)
    {
        $days = $request->input('days', 7);
        
        // Ejecutar el comando de refresco de estadísticas
        try {
            \Illuminate\Support\Facades\Artisan::call('analytics:refresh', [
                '--days' => $days
            ]);
            
            $output = \Illuminate\Support\Facades\Artisan::output();
            \Illuminate\Support\Facades\Log::info('Refresco de análisis: ' . $output);
            
            return redirect()->route('admin.analytics.content')
                ->with('success', "Se han refrescado las estadísticas de los últimos {$days} días correctamente.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al refrescar análisis: ' . $e->getMessage());
            
            return redirect()->route('admin.analytics.content')
                ->with('error', 'Ha ocurrido un error al refrescar las estadísticas: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualizar específicamente los contadores de compras
     */
    public function refreshPurchases()
    {
        try {
            // Primero verificamos si existe la columna purchases en la tabla store_items
            if (!Schema::hasColumn('store_items', 'purchases')) {
                return redirect()->route('admin.analytics.content')
                    ->with('error', 'La columna "purchases" no existe en la tabla store_items. Por favor, ejecute las migraciones pendientes.');
            }
            
            // Limpiar contadores existentes
            DB::update('UPDATE store_items SET purchases = 0');
            
            // Contar compras por item_id
            $purchaseCounts = UserInventory::select('item_id', DB::raw('count(*) as count'))
                ->groupBy('item_id')
                ->get();
                
            // Actualizar contadores
            $totalProcessed = 0;
            foreach ($purchaseCounts as $purchase) {
                $affected = DB::update(
                    'UPDATE store_items SET purchases = ? WHERE item_id = ?',
                    [$purchase->count, $purchase->item_id]
                );
                $totalProcessed += $affected;
            }
            
            // Redirigir con mensaje de éxito
            return redirect()->route('admin.analytics.content')
                ->with('success', "Los contadores de compras se han actualizado correctamente. Total de items actualizados: {$totalProcessed}");
                
        } catch (\Exception $e) {
            // Registrar error en log
            \Illuminate\Support\Facades\Log::error('Error al actualizar contadores de compras: ' . $e->getMessage());
            
            // Redirigir con mensaje de error
            return redirect()->route('admin.analytics.content')
                ->with('error', 'Ha ocurrido un error al actualizar los contadores de compras: ' . $e->getMessage());
        }
    }
} 