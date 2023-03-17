<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::inRandomOrder()->first();
        return [
            "sku" => (string) rand(0, 1000000000000),
            "pcode" =>  (string) rand(0, 1000000000000),
            "quantity" =>  rand(0, 10000),
            "price_per_unit" => rand(1, 10000),
            "product_id" => $product->id
        ];
    }
}
