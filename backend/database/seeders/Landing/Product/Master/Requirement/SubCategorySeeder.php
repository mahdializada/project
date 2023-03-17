<?php

namespace Database\Seeders\Landing\Product\Master\Requirement;

use App\Models\Landing\Master\Product\Requirement\RequirementSubCategory;
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
        RequirementSubCategory::factory(20)->create();
    }
}