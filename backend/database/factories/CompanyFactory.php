<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $location=Location::inRandomOrder()->first()->id;
        return [
            'company_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'logo' => $this->faker->image(),
            'investment_type' => $this->faker->randomElement(['main company ','others']),
            'status' => $this->faker->randomElement(['pending ','active','inactive','blocked','removed']),
            'pre_status' => $this->faker->randomElement(['pending ','active','inactive','blocked']),
            'location_id'=>$location,
        ];
    }
}
