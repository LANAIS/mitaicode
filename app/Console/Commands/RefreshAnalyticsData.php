<?php

namespace App\Console\Commands;

use App\Models\DailyStatistic;
use App\Models\User;
use App\Models\Challenge;
use App\Models\StoreItem;
use App\Models\UserInventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RefreshAnalyticsData extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'analytics:refresh {--days=7 : Número de días para refrescar}';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Refresca todas las estadísticas analíticas para un período de tiempo';

    /**
     * Ejecutar el comando de consola.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Refrescando estadísticas para los últimos {$days} días...");
        
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();
        
        // Crear un periodo de días
        $period = \Carbon\CarbonPeriod::create($startDate, '1 day', $endDate);
        
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $this->info("Procesando fecha: {$dateStr}");
            
            $this->refreshUserStatistics($dateStr);
            $this->refreshChallengeStatistics($dateStr);
            $this->refreshStoreStatistics($dateStr);
        }
        
        $this->info('¡Estadísticas actualizadas correctamente!');
        return Command::SUCCESS;
    }
    
    /**
     * Refrescar estadísticas de usuarios para una fecha.
     */
    private function refreshUserStatistics($dateStr)
    {
        // Eliminar estadísticas existentes
        DailyStatistic::where('date', $dateStr)
            ->whereIn('metric_type', ['user_registrations', 'user_activity'])
            ->delete();
        
        // Recalcular y guardar
        $newUsers = User::whereDate('created_at', $dateStr)->count();
        DailyStatistic::incrementStat($dateStr, 'user_registrations', null, $newUsers);
        $this->info("- Usuarios nuevos: {$newUsers}");
        
        $activeUsers = User::whereDate('last_login_at', $dateStr)->count();
        DailyStatistic::incrementStat($dateStr, 'user_activity', null, $activeUsers);
        $this->info("- Usuarios activos: {$activeUsers}");
    }
    
    /**
     * Refrescar estadísticas de desafíos para una fecha.
     */
    private function refreshChallengeStatistics($dateStr)
    {
        // Eliminar estadísticas existentes
        DailyStatistic::where('date', $dateStr)
            ->whereIn('metric_type', ['challenge_completions', 'challenge_starts', 'challenges_in_progress'])
            ->delete();
        
        // Recalcular y guardar
        $completedChallenges = DB::table('challenge_user')
            ->whereDate('completed_at', $dateStr)
            ->where('status', 'completed')
            ->count();
        DailyStatistic::incrementStat($dateStr, 'challenge_completions', null, $completedChallenges);
        $this->info("- Desafíos completados: {$completedChallenges}");
        
        $startedChallenges = DB::table('challenge_user')
            ->whereDate('started_at', $dateStr)
            ->count();
        DailyStatistic::incrementStat($dateStr, 'challenge_starts', null, $startedChallenges);
        $this->info("- Desafíos iniciados: {$startedChallenges}");
        
        // Desafíos en progreso (para esa fecha)
        $inProgressChallenges = DB::table('challenge_user')
            ->where('status', 'in_progress')
            ->whereDate('updated_at', $dateStr)
            ->count();
        DailyStatistic::incrementStat($dateStr, 'challenges_in_progress', null, $inProgressChallenges);
        $this->info("- Desafíos en progreso: {$inProgressChallenges}");
        
        // Por tipo de desafío
        $challengeTypes = Challenge::select('type')
            ->distinct()
            ->whereNotNull('type')
            ->pluck('type')
            ->toArray();
            
        foreach ($challengeTypes as $type) {
            $typeCompletions = DB::table('challenge_user')
                ->join('challenges', 'challenge_user.challenge_id', '=', 'challenges.id')
                ->where('challenges.type', $type)
                ->whereDate('challenge_user.completed_at', $dateStr)
                ->where('challenge_user.status', 'completed')
                ->count();
                
            DailyStatistic::incrementStat($dateStr, 'challenge_completions_by_type', $type, $typeCompletions);
            $this->info("  - Tipo {$type}: {$typeCompletions}");
        }
    }
    
    /**
     * Refrescar estadísticas de tienda para una fecha.
     */
    private function refreshStoreStatistics($dateStr)
    {
        // Eliminar estadísticas existentes
        DailyStatistic::where('date', $dateStr)
            ->whereIn('metric_type', ['store_purchases', 'store_revenue'])
            ->delete();
        
        // Recalcular y guardar
        $storeTransactions = UserInventory::whereDate('created_at', $dateStr)->count();
        DailyStatistic::incrementStat($dateStr, 'store_purchases', null, $storeTransactions);
        $this->info("- Compras en tienda: {$storeTransactions}");
        
        $revenue = UserInventory::join('store_items', 'user_inventories.item_id', '=', 'store_items.item_id')
            ->whereDate('user_inventories.created_at', $dateStr)
            ->sum(DB::raw('store_items.price'));
        DailyStatistic::incrementStat($dateStr, 'store_revenue', null, $revenue);
        $this->info("- Ingresos: {$revenue}");
        
        // Por categoría
        $categories = StoreItem::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();
            
        foreach ($categories as $category) {
            $categoryPurchases = UserInventory::join('store_items', 'user_inventories.item_id', '=', 'store_items.item_id')
                ->where('store_items.category', $category)
                ->whereDate('user_inventories.created_at', $dateStr)
                ->count();
                
            DailyStatistic::incrementStat($dateStr, 'store_purchases_by_category', $category, $categoryPurchases);
            $this->info("  - Categoría {$category}: {$categoryPurchases}");
        }
    }
} 