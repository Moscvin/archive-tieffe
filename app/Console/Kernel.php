<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\FactureCron::class,
        Commands\ReportsImagesResizeCron::class,
        Commands\UpdateOperationsCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        /*$schedule->command('facture:cron')
            //->everyMinute();
            ->dailyAt('09:10');
        */
            
        $schedule->command('reports_photos_resize:cron')
            ->dailyAt('04:55');

        //* * * * * php /path/to/artisan schedule:run 1>> /dev/null 2>&1
        //php artisan facture:cron
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
}
