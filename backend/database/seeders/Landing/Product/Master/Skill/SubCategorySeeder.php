<?php

namespace Database\Seeders\Landing\Product\Master\Skill;

use App\Models\Landing\Master\Product\Skill\SkillSubCategory;
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
        SkillSubCategory::factory(20)->create();
    }
}
