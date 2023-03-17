<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\ProductStudyModel;
use Illuminate\Database\Seeder;

class ProductStudyModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductStudyModel::factory(50)->create();
    }
}
