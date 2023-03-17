<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\ProductSourceValue;
use Illuminate\Database\Seeder;

class ProductSourcValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ProductSourceValue::factory(10)->create();
    }
}
