<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Product;
use App\Models\SingleSales\ProductAttribute;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ProductAttribute::factory(20)->create();
    }
}
