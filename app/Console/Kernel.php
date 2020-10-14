<?php
namespace App\Console;

use CjsConsole\Scheduling\Schedule;
use CjsConsole\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\Test', //测试命令
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('mytest:test')->cron('* * * * *')->environments('dev');

    }

}