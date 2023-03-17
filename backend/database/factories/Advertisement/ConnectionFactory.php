<?php

namespace Database\Factories\Advertisement;

use App\Models\SingleSales\Ispp;
use App\Models\Advertisement\Project;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\Application;
use App\Models\Company;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "pcode" => $this->faker->name(),
            "code" => rand(1000, 99999999),
            "generated_link" => $this->faker->text(),
            "landing_page_link" => $this->faker->text(),
            "ad_id" => $this->faker->name(),
            "media_link" => $this->faker->name(),
            "ispp_id" =>  Ispp::inRandomOrder()->first()->id,
            "project_id" =>  Project::inRandomOrder()->first()->id,
            "application_id" => Application::inRandomOrder()->first()->id,
            "platform_id" => Platform::inRandomOrder()->first()->id,
            "company_id" => Company::inRandomOrder()->first()->id,
            "country_id" => Country::inRandomOrder()->first()->id,
        ];
    }
}
