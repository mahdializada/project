<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\InterestValue;
use App\Models\ProductManagement\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::inRandomOrder()->first();
        $interest_value = InterestValue::inRandomOrder()->first();
        return [
            "interest_value_id" => $interest_value->id,
            "interestable_type" => Product::class,
            "interestable_id" => $product->id
        ];
    }
}
