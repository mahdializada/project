<?php

namespace Database\Seeders\Landing;

use App\Models\Landing\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Skill::factory(20)->create();
    }
}
