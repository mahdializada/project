<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\AttributeCategory;
use App\Models\SingleSales\ProductInventory;
use Illuminate\Database\Seeder;

class ProductInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ProductInventory::factory(10)->create();
    }
}
