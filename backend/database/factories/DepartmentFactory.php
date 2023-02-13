<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $company=Company::inRandomOrder()->first()->id;
        return [
            'company_id'=>$company,
            'logo'=>$this->faker->name(),
            'Department_name'=>$this->faker->name(),
            'status' => $this->faker->randomElement(['pending ','active','inactive','blocked']),
        ];
    }
}
