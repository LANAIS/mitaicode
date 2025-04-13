<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These cron jobs are run in the Artisan command line when a web server
     * is being restarted. This is a great place to handle scheduled tasks.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Notificaciones de correo electrónico - se ejecutan cada hora
        $schedule->command('emails:send-notifications')->hourly();
        
        // Actualizar rachas de usuarios diariamente a medianoche
        $schedule->command('streaks:update')->dailyAt('00:01');
        
        // Comandos de gamificación
        $schedule->command('gamification:process-streaks')->dailyAt('00:05');
        $schedule->command('gamification:rotate-leaderboards')->dailyAt('00:10');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateDailyStatistics::class,
        Commands\PopulateAnalyticsData::class,
        Commands\RefreshAnalyticsData::class,
        Commands\CreateDefaultNotifications::class,
    ];
} 