<?php

namespace App\Console\Commands;

use Database\Seeders\SubSystemColumnSeeder;
use Illuminate\Console\Command;

class insertColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:columns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add subsystem columns';

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
        $this->call(SubSystemColumnSeeder::class);
        $this->info("Columns has been inserted successfully!");
        return 0;
    }
}
