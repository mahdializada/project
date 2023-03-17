<?php

namespace Database\Seeders;

use App\Models\Sourcer;
use Illuminate\Database\Seeder;

class SourcerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Sourcer::factory(20)->create();
    }
}
