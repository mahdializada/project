<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Product as SingleSalesProduct;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        SingleSalesProduct::factory(10)->create();
    }
}
