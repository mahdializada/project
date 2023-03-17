<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Product;
use App\Models\ProductManagement\ReservationRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::inRandomOrder()->first();
        $reservaion_request = ReservationRequest::inRandomOrder()->first();
        return [
            "quantity" => rand(1, 1000),
            "status" => $this->faker->randomElement(['in_process', 'oreder_made', 'recieved_in_warehouse', 'failed']),
            "prograssive" => rand(0, 100),
            "product_id" => $product->id,
            "reservation_request_id" => $reservaion_request->id,

        ];
    }
}
