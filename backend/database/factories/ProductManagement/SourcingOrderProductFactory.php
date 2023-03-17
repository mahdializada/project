<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Product;
use App\Models\ProductManagement\SourcingOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class SourcingOrderProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sourcing_order = SourcingOrder::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();
        return [
            "approx_quantity" => rand(0, 1000),
            "target_cost" => rand(0, 1000),
            "variation_note" => $this->faker->text(200),
            "product_note" => $this->faker->text(200),
            "reference" => $this->faker->text(1000),
            "status" => $this->faker->randomElement(
                ['canceled', 'deleted', 'to_be_found']
            ),
            "sourcing_order_id" =>  $sourcing_order->id,
            "product_id" => $product->id
        ];
    }
}
