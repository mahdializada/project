<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\IpaAddPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

class IpaAddPlacementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $socialMedia=IpaAddPlatform::inRandomOrder()->first();
        return [
            "placement_name"=>$this->faker->name,
            "add_platform_id"=>$socialMedia->id,
        ];
    }
}
