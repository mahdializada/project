<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\ProductStudyEvaluation;
use Illuminate\Database\Seeder;

class ProductStudyEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductStudyEvaluation::factory(20)->create();

    }
}
