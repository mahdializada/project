<?php

namespace Database\Seeders\Landing\Product\Master\Skill;


use App\Models\Landing\Master\Product\Skill\SkillCategory;
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
        SkillCategory::factory(20)->create();
    }
}
