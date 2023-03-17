<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Attribute;
use App\Models\ProductManagement\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $attribute = Attribute::inRandomOrder()->first();
        $inventory = Inventory::inRandomOrder()->first();
        return [
            'attribute_value' => $this->faker->firstName,
            'inventory_id' => $inventory->id,
            'attribute_id' => $attribute->id
        ];
    }
}
