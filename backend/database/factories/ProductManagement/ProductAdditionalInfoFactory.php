<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\ProductList;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAdditionalInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ispp_product_list = ProductList::inRandomOrder()->first();
        return [
            "type" => $this->faker->randomElement(['description', 'product_content_and_advertisement_reference', 'financial_informationa_and_suggestion', 'competitor_advertiser_reference']),
            "reference_text" => $this->faker->text(50),
            "ispp_product_list_id" => $ispp_product_list->id
        ];
    }
}
