<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\Attribute;
use App\Models\SingleSales\Product;
use App\Models\SingleSales\ProductInventory;
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
        $attribute = Attribute::inRandomOrder()->limit(10)->get();
        $product_inventory = Product::inRandomOrder()->limit(10)->get();

        return [
            //
            "product_id" => Product::inRandomOrder()->first()->id,
            "attribute_id" => Attribute::inRandomOrder()->first()->id,
            'product_inventory_id'=>ProductInventory::inRandomOrder()->first()->id,
            'attribute_value'=>$this->faker->name,
        ];
    }
}
