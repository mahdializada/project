<?php

namespace Database\Seeders\Landing\Product\Master;

use App\Models\Landing\Master\Product\ProductSubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductSubCategory::factory(20)->create();
    }
}