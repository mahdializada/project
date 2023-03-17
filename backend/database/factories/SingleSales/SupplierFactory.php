<?php

namespace Database\Factories\SingleSales;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // $category = Category::inRandomOrder()->first();
        // $supplier = Sup::inRandomOrder()->first();
        return [
            "code" => rand(100000, 9999999999),

        ];
    }
}
