<?php

namespace App\Jobs;

use App\Repositories\Advertisement\ConnectionRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class GenerateLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private    $request;

    public function __construct($data,  $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $repository = new ConnectionRepository();
        return $repository->update($this->request);
    }
    public function failed($exception)
    {

        // throw new Exception($exception->getMessage(), 6000);
        dd($exception->getMessage());
        // $error = ['status' => 'faild', 'faild_message' => $exception->getMessage()];
        // $error = array_merge($error, $this->extra_data);
        // SendRefreshAdsEvent::dispatch($error);
    }
}
