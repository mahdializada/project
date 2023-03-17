<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Ipa;
use App\Models\SingleSales\IsppTargetSaleCountry;
use Illuminate\Database\Seeder;

class IsppTargetSaleCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        IsppTargetSaleCountry::factory(10)->create();
    }
}
