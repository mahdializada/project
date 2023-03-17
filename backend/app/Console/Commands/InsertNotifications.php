<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class InsertNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('notifications')->truncate();
        Schema::enableForeignKeyConstraints();
        $notifications = json_decode(File::get(resource_path() . "/json/notifications.json"));

        foreach ($notifications as $key => $notification) {
            $notificationModel = new Notification();
            $notification = (array)$notification;
            $notificationModel->create([
                "title" => $notification['title'],
                "description" => $notification['description'],
                "icon" => $notification['icon'],
                "type" => $notification['type'],
            ]);
        }

        return 0;
    }
}
