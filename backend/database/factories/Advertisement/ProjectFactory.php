<?php

namespace Database\Factories\Advertisement;

use App\Models\Company;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $countries = Country::whereHas("companies")->pluck("id");
        $companies = Company::pluck("id");
        return [
            "name" => $this->faker->name(),
            "domain" => $this->faker->domainName(),
            "code" => rand(1000, 99999999),
            "countries" => json_encode($countries),
            "companies" => json_encode($companies),
        ];
    }
}
