<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\QuantityReservationProducts;
use Illuminate\Database\Seeder;

class QuantityReservationProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        QuantityReservationProducts::factory(10)->create();
    }
}
