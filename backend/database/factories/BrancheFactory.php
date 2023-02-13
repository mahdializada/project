<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branche>
 */
class BrancheFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $location=Location::inRandomOrder()->first()->id;
        $company=Company::inRandomOrder()->first()->id;
        return [
            'company_id'=>$company,
            'location_id'=>$location,
            'branche_name'=>$this->faker->name(),
        ];
    }
}
