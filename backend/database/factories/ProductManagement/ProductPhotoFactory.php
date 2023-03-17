<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductPhotoFactory extends Factory
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
            "path" => $this->faker->imageUrl,
            "product_id" => $product->id
        ];
    }
}
