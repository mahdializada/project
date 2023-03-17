<?php

namespace Database\Factories\SingleSales;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SingleSales\ProductSource;

class ProductSourceValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ProductSource = ProductSource::inRandomOrder()->first();
        
        return [
            "product_source_id" => $ProductSource->id,
            "value" => $this->faker->unique->name,

        
        ];
    }
}
