<?php

namespace App\Console;

use App\Http\Controllers\Advertisement\ConnectionController;
use App\Jobs\FetchDailyAdsFromServer;
use App\Models\TempFile;
use App\Repositories\Advertisement\AdvertisementUtil;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Http\Request;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function () {
            $yesterday = Carbon::yesterday();
            $request = new Request([
                'dates' => $yesterday->toDateString()
            ]);

            AdvertisementUtil::addPlatformIntoQueue($request);
        })->name('update_yesterday_ads')->evenInMaintenanceMode()
            ->withoutOverlapping()->dailyAt("02:30")->runInBackground();

        $schedule->call(function () {
            AdvertisementUtil::addPlatformRequestsIntoQueue();
        })->name('store_today_ads')->evenInMaintenanceMode()
            ->withoutOverlapping()->dailyAt("00:05")->runInBackground();


        // refresh inactive ads
        $schedule->call(function () {

            ConnectionController::insertInactiveAds();
        })->name('store_today_inactive_ads')->evenInMaintenanceMode()
            ->withoutOverlapping()->dailyAt("00:40")->runInBackground();

        $schedule->command('auth:clear-resets')->hourly();
        $schedule->call(function () {
            $q = TempFile::get();
            foreach ($q as $q) {
                $q->delete();
            }
        })->sundays();


        // refresh disabled ads
        $schedule->call(function () {
            AdvertisementUtil::refreshDisabledAds();
        })->name('refresh_disabled_ads')->evenInMaintenanceMode()
            ->withoutOverlapping()->dailyAt("06:25")->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
