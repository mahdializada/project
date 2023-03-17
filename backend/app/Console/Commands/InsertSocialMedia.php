<?php

namespace App\Console\Commands;

use App\Models\SocialMedia;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\User;
class InsertSocialMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:socialmedia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert All Social Media';

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
        DB::beginTransaction();
        try {
            $socialMediaFile = json_decode(File::get(resource_path() . "/json/socialMedia.json"));
            foreach ($socialMediaFile as $socialMedia) {
                $user = User::inRandomOrder()->first();
                $socialMedia = (array)$socialMedia;
                $socialMediaModel = new SocialMedia();
                $attributes = array_intersect_key($socialMedia, array_flip($socialMediaModel->getFillable()));
                $attributes['code'] = rand(100000, 9999999999);
                $attributes['created_by'] = $user->id;
                $attributes['updated_by'] = $user->id;
                $socialMediaModel->create($attributes);
                // $socialMediaModel->create(['code'=> rand(100000, 9999999999),'name' => $socialMedia['name'], 'logo' => $socialMedia['logo'], 'url' => $socialMedia['url']]);
            }
            DB::commit();
            $this->info("All Social Media Inserted Successfully!");
        } catch (\Exception $exception) {

            DB::rollBack();
            $this->info($exception);
            $this->info("Something went wrong!");
            $this->error("Please try again!");
        }
        return 1;
    }
}
