<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = Department::factory(1)->create();
        foreach ($departments as $department) {
            $companies = Company::query()->inRandomOrder()->take(1);
            $department->companies()->sync($companies->pluck("id")->toArray());
        }
    }
}
