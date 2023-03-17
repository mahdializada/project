<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\SourcingRequest;
use App\Models\SingleSales\Product;

use Illuminate\Database\Eloquent\Factories\Factory;


class SourcingRequestProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sourcingRequest = SourcingRequest::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();
        
        return [
            "sourcing_request_id" => $sourcingRequest->id,
            "product_id" => $product->id,
            "approx_quantity" =>  rand(10, 999),
            "target_cost" =>  rand(10, 999),
            "note" => $this->faker->unique->text,
            "reference" => json_encode([$this->faker->unique->text]),
            "status" => $this->faker->randomElement(["found", "not found"]),

        ];
    }
}
