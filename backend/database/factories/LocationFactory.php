<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $country= Country::inRandomOrder()->first()->id;
        return [
            'longitude' => $this->faker->longitude(),
            'latitude' => $this->faker->latitude(),
            'state_district' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'address_line' => $this->faker->address(),
            'country_id' => $country,


        ];
    }
}
