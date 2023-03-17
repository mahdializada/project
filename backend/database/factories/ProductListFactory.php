<?php

namespace Database\Factories;
use App\Models\Supplier;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => rand(100000, 999999999),
            'product_name'=>$this->faker->name,
            'product_code'=>"PRL".rand(10,100),
        ];
    }
}
