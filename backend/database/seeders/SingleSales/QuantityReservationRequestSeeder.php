<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\QuantityReservationRequest;
use App\Models\SingleSales\Supplier as SingleSalesSupplier;
use Illuminate\Database\Seeder;

class QuantityReservationRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        QuantityReservationRequest::factory(10)->create();
    }
}
