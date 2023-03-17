<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\AttributeCategory;
use Illuminate\Database\Seeder;

class AttributeCategorySeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AttributeCategory::factory(10)->create();
    }
}
