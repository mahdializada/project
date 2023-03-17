<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\SourcingRequestProduct;
use Illuminate\Database\Seeder;

class SourcingRequestProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        SourcingRequestProduct::factory(10)->create();
    }
}
