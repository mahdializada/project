<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = Team::factory(1)->create();
        $departments = Department::query()->inRandomOrder()->take(1);
        foreach ($teams as $team) {
            $team->departments()->sync($departments->pluck("id")->toArray());
        }
    }
}
