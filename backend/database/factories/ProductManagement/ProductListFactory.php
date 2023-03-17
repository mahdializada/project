<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Product;
use App\Models\ProductManagement\Request;
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
        $ispp_request = Request::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();
        return [
            "ispp_request_id" => $ispp_request->id,
            "product_id" => $product->id,
            "content_availability" => $this->faker->randomElement(['availabe', 'not_availabe'])
        ];
    }
}
