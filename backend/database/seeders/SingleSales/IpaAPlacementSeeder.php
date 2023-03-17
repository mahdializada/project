<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\IpaPlacement;
use Illuminate\Database\Seeder;

class IpaAPlacementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       IpaPlacement::factory(20)->create();
    }
}
