<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\SellingGoals;
use Illuminate\Database\Seeder;

class SellingGoalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SellingGoals::factory(10)->create();
    }
}
