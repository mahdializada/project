<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\ProductStudyFormComponent;
use Illuminate\Database\Seeder;

class ProductStudyFormComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductStudyFormComponent::factory(20)->create();
    }
}
