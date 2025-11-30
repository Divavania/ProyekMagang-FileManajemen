<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto-delete trash setiap hari jam 00:00 (tengah malam)
        $schedule->command('trash:auto-delete')->daily();
        
        // Alternatif jadwal lain (pilih salah satu):
        
        // Jalankan setiap hari jam 01:00 pagi
        // $schedule->command('trash:auto-delete')->dailyAt('01:00');
        
        // Jalankan setiap 12 jam (jam 00:00 dan 12:00)
        // $schedule->command('trash:auto-delete')->twiceDaily(0, 12);
        
        // Jalankan setiap 6 jam
        // $schedule->command('trash:auto-delete')->everySixHours();
        
        // Jalankan setiap jam
        // $schedule->command('trash:auto-delete')->hourly();
        
        // Jalankan setiap hari Senin jam 02:00
        // $schedule->command('trash:auto-delete')->weeklyOn(1, '02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}