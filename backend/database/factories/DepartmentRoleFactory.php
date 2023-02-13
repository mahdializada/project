<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DepartmentRole>
 */
class DepartmentRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $deprtment=Department::inRandomOrder()->first()->id;
        $role=Role::inRandomOrder()->first()->id;
        return [
            'department_id'=>$deprtment,
            'role_id'=>$role,

        ];
    }
}
