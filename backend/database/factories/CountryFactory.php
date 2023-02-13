<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'flag' => $this->faker->unique()->name,
            'name' => $this->faker->name,
            'capital' => $this->faker->name,
            'national' => $this->faker->name,
            'native' => $this->faker->lastName,
            'phone_code' => $this->faker->phoneNumber,
            'currency' => $this->faker->userName,
            'currency_symbol' => $this->faker->colorName,
            'region' => $this->faker->name,
            'sub_region' => $this->faker->name,
            'status' => $this->faker->randomElement(['pending ','active','inactive','blocked']),
            'timeZone' => $this->faker->locale,
        ];
    }
}
