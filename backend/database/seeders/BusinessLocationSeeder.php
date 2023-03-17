<?php

namespace Database\Seeders;

use App\Models\BusinessLocation;
use Illuminate\Database\Seeder;

class BusinessLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BusinessLocation::factory(1)->create();
    }
}
