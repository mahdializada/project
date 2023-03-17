<?php

namespace Database\Seeders\Landing\Product\Master;

use App\Models\Landing\Master\Product\ProductCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductCategory::factory(20)->create();
    }
}