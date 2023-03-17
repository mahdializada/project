<?php

namespace Database\Factories;

use App\Models\BusinessType;
use App\Models\Company;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "code" => rand(100000, 9999999999),
            "name" => "Teeb Alhoor",
            "logo" => $this->faker->unique->imageUrl,
            "email" => $this->faker->unique->email,
            "investment_type" => "Main Company",
            "status" => "active",
            "note" => $this->faker->unique->text,
            "created_by" => User::inRandomOrder()->first()->id,
            "updated_by" => User::inRandomOrder()->first()->id,
        ];
    }
}
