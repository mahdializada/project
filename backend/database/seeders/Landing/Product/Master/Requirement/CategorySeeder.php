<?php

namespace Database\Seeders\Landing\Product\Master\Requirement;


use App\Models\Landing\Master\Product\Requirement\RequirementCategory;
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
        RequirementCategory::factory(20)->create();
    }
}