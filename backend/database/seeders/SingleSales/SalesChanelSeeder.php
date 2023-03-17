<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\SalesChanel;
use Illuminate\Database\Seeder;

class SalesChanelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SalesChanel::factory(10)->create();
    }
}
