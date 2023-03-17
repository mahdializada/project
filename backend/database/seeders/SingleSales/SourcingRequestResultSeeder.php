<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\SourcingResult;
use Illuminate\Database\Seeder;

class SourcingRequestResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SourcingResult::factory(10)->create();
    }
}
