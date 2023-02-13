<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDepartment>
 */
class UserDepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $department= Department::inRandomOrder()->first()->id;
        $user= User::inRandomOrder()->first()->id;

        return [
            'department_id'=>$department,
            'user_id'=>$user,
        ];
    }
}
