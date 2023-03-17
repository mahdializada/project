<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

class IpaRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $request = Request::inRandomOrder()->first();
        return [
            "ad_policy_violation" => $this->faker->randomElement(['none', 'low', 'average', 'high']),
            "work_priority" =>  $this->faker->randomElement(['high', 'medium', 'low']),
            "is_publication_period_limited" => rand(0, 1),
            "publication_start_period" => $this->faker->date(),
            "publication_end_period" =>  $this->faker->date(),
            "intensify_result_confirmation" => rand(0, 1),
            "is_daily_activition_period_limited" => rand(0, 1),
            "start_hour" => $this->faker->time,
            "end_hour" => $this->faker->time,
            "prograssive" => rand(0, 100),
            "status" => $this->faker->randomElement([
                'pending', 'reject', 'in_creation', 'in_testing', 'sales_moving', 'sales_unstable', 'stopped', 'under_developing', 'in_hold', 'canceld', 'delete'
            ]),
            "ispp_request_id" => $request->id
        ];
    }
}
