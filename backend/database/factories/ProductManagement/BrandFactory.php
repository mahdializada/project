<?php

namespace Database\Factories\ProductManagement;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $country = Country::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        return [
            "name" => $this->faker->name,
            "code" => rand(10000, 100000),
            "status" => $this->faker->randomElement(['active', 'inactive']),
            "is_approved" => rand(0, 1),
            "country_id" => $country->id,
            "created_by" => $user->id
        ];
    }
}
