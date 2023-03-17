<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\TargetCountry;
use Illuminate\Database\Seeder;

class TargetCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TargetCountry::factory(20)->create();
    }
}
