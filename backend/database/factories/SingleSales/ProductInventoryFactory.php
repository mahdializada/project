<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductInventoryFactory extends Factory
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
            //
            "product_id" => $product->id,
            "sku" => $this->faker->unique->name,
            "quantity" =>  rand(10, 999),
            "price_per_unit" =>  rand(10, 999),
        ];
    }
}
