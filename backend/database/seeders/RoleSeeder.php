<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::factory(1)->create();
        $departments = Department::query()->inRandomOrder()->take(1);
        foreach ($roles as $role) {
            $role->departments()->sync($departments->pluck("id")->toArray());
        }
    }
}
