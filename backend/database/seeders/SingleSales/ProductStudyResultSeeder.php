<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\ProductStudyResult;
use Illuminate\Database\Seeder;

class ProductStudyResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ProductStudyResult::factory(10)->create();
    }
}
