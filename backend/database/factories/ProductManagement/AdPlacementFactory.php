<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\AdPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdPlacementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $platform = AdPlatform::inRandomOrder()->first();
        return [
            "name" => $this->faker->name,
            "platform_id" => $platform->id
        ];
    }
}
