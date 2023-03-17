<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\ProductRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchasedProductInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product_request = ProductRequest::inRandomOrder()->first();
        return [
            "order_no" => rand(0, 100000000),
            "arrival_time" => $this->faker->date(),
            "purchase_date" => $this->faker->date(),
            "product_requests_id" => $product_request->id
        ];
    }
}
