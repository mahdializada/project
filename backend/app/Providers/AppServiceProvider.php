<?php

namespace App\Providers;

use App\Events\SendRefreshAdsEvent;
use App\Events\SendRefreshCrmEvent;
use Exception;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Queue\Events\JobFailed;
use Laravel\Telescope\TelescopeApplicationServiceProvider;


use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (class_exists(TelescopeApplicationServiceProvider::class)) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $mainPath = database_path('migrations');
        $directories = glob($mainPath . '/*', GLOB_ONLYDIR);
        $paths = array_merge([$mainPath], $directories);

        $this->loadMigrationsFrom($paths);

        Queue::after(function (JobProcessed $event) {
            $payload = $event->job->payload();
            if (is_array($payload) && array_key_exists("displayName", $payload)) {

                // if ($payload["displayName"] == "App\Jobs\SwitchByPlatformName") {
                //     $raw_data = $event->job->payload()["data"]["command"];
                //     $extra_data = unserialize($raw_data)->extra_data;
                //     SendRefreshAdsEvent::dispatch($extra_data);
                // }
                if ($payload["displayName"] == "App\Jobs\RefreshCrmJob") {
                    // try {
                    //     SendRefreshCrmEvent::dispatch(['data' => 'data']);
                    //     //code...
                    // } catch (\Throwable $th) {
                    //     throw new Exception($th->getMessage(), 500);
                    // }
                }
            }
        });

        // Queue::failing(function (JobFailed $event) {

        //     file_put_contents('test.js', $event->job->payload(), FILE_APPEND);
        // });
    }
}
