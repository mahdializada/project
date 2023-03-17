<?php

namespace Database\Factories\Advertisement;

use App\Models\Company;
use App\Models\Advertisement\PlatformName;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "platform_name" => 'facebook',
            "code" => rand(1000, 99999999),
            "platform_key" => $this->faker->name(),
            "company_id" => Company::inRandomOrder()->first()->id,
        ];
    }
}
