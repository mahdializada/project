<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\SourcingOrderProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class SourcingOrderResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sourcing_order_product = SourcingOrderProduct::inRandomOrder()->first();
        return [
            "available_quantities" => rand(0, 100),
            "cost" => rand(1, 1000),
            "shipping_method" => $this->faker->randomElement(
                ['air', 'land', 'sea']
            ),
            "delivery_time" => $this->faker->date(),
            "quantity_model" => $this->faker->randomElement(
                ['whole_sale', 'retail']
            ),
            "shipping_cost" => rand(0, 100),
            "product_note" => $this->faker->text(200),
            "import_restrictions" => $this->faker->text(200),
            "import_note" => $this->faker->text(200),
            "sourcing_note" => $this->faker->text(200),
            "progressive" => rand(0, 100),
            "sourcing_order_product_id" => $sourcing_order_product->id,
            "supplier_id" => null
        ];
    }
}
