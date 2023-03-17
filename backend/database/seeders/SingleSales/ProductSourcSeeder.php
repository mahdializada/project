<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\ProductSource;
use Illuminate\Database\Seeder;

class ProductSourcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ProductSource::factory(3)->create();
    }
}
