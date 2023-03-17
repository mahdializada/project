<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\ProductSource;
use Illuminate\Database\Eloquent\Factories\Factory;

class SourceValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product_source = ProductSource::inRandomOrder()->first();
        return [
            "value" => $this->faker->text(20),
            "product_source_id" => $product_source->id
        ];
    }
}
