<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateUserLoginData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-login-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los datos de última conexión para testear analíticas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Actualizando datos de última conexión...');
        
        // Obtenemos todos los usuarios
        $users = User::all();
        $totalUsers = $users->count();
        
        if ($totalUsers == 0) {
            $this->error('No hay usuarios en el sistema');
            return 1;
        }
        
        $this->info("Total de usuarios: {$totalUsers}");
        
        // Configuramos diferentes fechas de login para simular actividad
        $bar = $this->output->createProgressBar($totalUsers);
        $bar->start();
        
        // Dividimos a los usuarios en grupos
        // - 20% activos hoy
        // - 15% activos en los últimos 7 días
        // - 10% activos en los últimos 30 días
        // - El resto inactivos por más de 30 días
        
        $activeToday = (int)($totalUsers * 0.2);
        $activeWeek = (int)($totalUsers * 0.15);
        $activeMonth = (int)($totalUsers * 0.1);
        $inactive = $totalUsers - $activeToday - $activeWeek - $activeMonth;
        
        $count = 0;
        
        foreach ($users as $user) {
            if ($count < $activeToday) {
                // Activos hoy (últimas 24 horas)
                $lastLogin = Carbon::now()->subHours(rand(1, 23));
            } elseif ($count < ($activeToday + $activeWeek)) {
                // Activos en la última semana
                $lastLogin = Carbon::now()->subDays(rand(2, 7));
            } elseif ($count < ($activeToday + $activeWeek + $activeMonth)) {
                // Activos en el último mes
                $lastLogin = Carbon::now()->subDays(rand(8, 30));
            } else {
                // Inactivos por más de 30 días
                $lastLogin = Carbon::now()->subDays(rand(31, 365));
            }
            
            $user->update(['last_login_at' => $lastLogin]);
            $count++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\nDatos de login actualizados con éxito!");
        
        // Resumen
        $this->info("\nResumen de actualización:");
        $this->info("- Usuarios activos hoy: {$activeToday}");
        $this->info("- Usuarios activos última semana: {$activeWeek}");
        $this->info("- Usuarios activos último mes: {$activeMonth}");
        $this->info("- Usuarios inactivos: {$inactive}");
        
        return 0;
    }
}
