<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Brand;
use App\Models\ProductManagement\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $category = Category::inRandomOrder()->first();
        $brand = Brand::inRandomOrder()->first();
        return [
            "sku" => (string) rand(1, 999999090909),
            "pcode" => (string) rand(1, 999999090909),
            "type" => $this->faker->randomElement(
                ["phisycal", "digital"]
            ),
            "name" => $this->faker->name,
            "unit" =>  $this->faker->randomElement(
                ["cm", "kg", 'lt']
            ),
            "description" => $this->faker->text,
            "vat_tax" => rand(1, 1000),
            "available" =>  $this->faker->randomElement(
                ["now", "comming_soon"]
            ),
            "number_of_sales" => rand(1, 1000000),
            "is_published" => rand(0, 1),
            "created_by" => $user->id,
            "category_id" => $category->id,
            "brand_id" => $brand->id,
            "supplier_id" => null,
        ];
    }
}
