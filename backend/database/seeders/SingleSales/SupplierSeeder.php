<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Supplier as SingleSalesSupplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        SingleSalesSupplier::factory(10)->create();
    }
}
