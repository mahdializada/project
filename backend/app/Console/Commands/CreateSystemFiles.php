<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Company;
use App\Models\SystemFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CreateSystemFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:system-files';

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
        DB::beginTransaction();
        try {
            $systemFiles = json_decode(File::get(resource_path() . "/json/systemFiles.json"));
            $companies = Company::all()->toArray();
            foreach ($companies as $company){
                foreach ($systemFiles as $systemFile) {
                    $userId = User::inRandomOrder()->first()->id;
                    $systemFile = (array)$systemFile;
                    $systemFileModel = new SystemFile();
                    $attributes = array_intersect_key($systemFile, array_flip($systemFileModel->getFillable()));
                    $attributes['code'] = rand(100000, 9999999999);
                    $attributes['created_by'] = $userId;
                    $attributes['updated_by'] = $userId;
                    $attributes['company_id'] = $company['id'];
                    $attributes['sub_system_id']  = 'add8e981-bab0-4e43-9603-75d029db0206';
                    $systemFileModel->create($attributes);
                }
            }
            DB::commit();
            $this->info("All System Folders Inserted Successfully!");
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->info($exception);
            $this->info("Something went wrong!");
            $this->error("Please try again!");
        }
        return 1;
    }
}
