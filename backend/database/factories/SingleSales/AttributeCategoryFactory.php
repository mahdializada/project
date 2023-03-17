<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\Attribute;
use App\Models\SingleSales\Category;
use App\Models\SingleSales\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        // $country = Country::inRandomOrder()->first();
        $attribute = Attribute::inRandomOrder()->limit(10)->get();
        $category = Category::inRandomOrder()->limit(10)->get();

        return [
            "attribute_id" => $attribute[rand(0, 9)]->id,
            "category_id" => $category[rand(0, 9)]->id,
        ];
    }
}
