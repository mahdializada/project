<?php

namespace Database\Factories\ProductManagement;

use Illuminate\Database\Eloquent\Factories\Factory;

class SalesChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "channel_name" => $this->faker->name
        ];
    }
}
