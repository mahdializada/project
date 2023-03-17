<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\AdPlatform;
use App\Models\ProductManagement\IpaRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformTargetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $platform = AdPlatform::inRandomOrder()->first();
        $ipa_request = IpaRequest::inRandomOrder()->first();
        return [
            "target_gender" => $this->faker->randomElement(['male', 'female', 'all']),
            "start_target_age" => rand(10, 100),
            "end_target_age" => rand(10, 100),
            "target_note" => $this->faker->text(100),
            "budget_type" => $this->faker->randomElement(['limited', 'unlimited']),
            "budget_time_type" => $this->faker->randomElement(['daily', 'weekly', 'monthly']),
            "budget" => rand(1, 10000),
            "target_cost_per_order" => rand(1, 100),
            "platform_id" => $platform->id,
            "ipa_request_id" => $ipa_request->id
        ];
    }
}
