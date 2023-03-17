<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\IpaAddPlacement;
use App\Models\SingleSales\IpaAddPlatform;
use Illuminate\Database\Seeder;

class IpaAddPlacementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       IpaAddPlacement::factory(20)->create();
    }
}
