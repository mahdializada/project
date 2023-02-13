<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $department=Department::inRandomOrder()->first()->id;
        return [
            'department_id'=>$department,
            'team_name'=>$this->faker->name(),
            'logo'=>$this->faker->name(),
            'note'=>$this->faker->name(),
            'status' => $this->faker->randomElement(['pending ','active','inactive','blocked','warning']),
            'prev_status' => $this->faker->randomElement(['pending ','active','inactive','blocked','warning']),
        ];
    }
}
