<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\ProductStudyFormFulfillment;
use Illuminate\Database\Seeder;

class ProductStudyFormFulfillmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductStudyFormFulfillment::factory(20)->create();
    }
}
