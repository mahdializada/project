<?php

namespace Database\Factories\SingleSales;
use Illuminate\Database\Eloquent\Factories\Factory;
class ProductSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "parent_id" => "",
            "source_name" => $this->faker->unique->name,
        
        ];
    }
}
