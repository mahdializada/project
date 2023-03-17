<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Product as SingleSalesProduct;
use App\Models\SingleSales\ProductStudy;
use Illuminate\Database\Seeder;

class ProductStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ProductStudy::factory(10)->create();
    }
}
