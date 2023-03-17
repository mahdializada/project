<?php

namespace Database\Factories;

use App\Models\BusinessLocation;
use App\Models\Company;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $cratedBy            = User::inRandomOrder()->first();
        $updatedBy           = User::inRandomOrder()->first();
        $company             = Company::inRandomOrder()->first();
        $country             = Country::inRandomOrder()->first();
        $state               = State::inRandomOrder()->first();
        return [
            "name" => $this->faker->name,
            "code" => rand(11111111111, 999999999),
            "email" => $this->faker->email,
            "address" => $this->faker->address,
            "note" => $this->faker->paragraph,
            "map_link" => $this->faker->url,
            "status" => 'active',
            "state_id" => $state->id,
            "company_id" => $company->id,
            "country_id" => $country->id,
            "updated_by" => $updatedBy->id,
            "created_by" => $cratedBy->id,
        ];
    }
}
