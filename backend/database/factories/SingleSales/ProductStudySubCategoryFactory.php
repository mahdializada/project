<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\ProductStudyCategory;
use App\Models\SingleSales\ProductStudySubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStudySubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $category= ProductStudyCategory::inRandomOrder()->first();
        return [
           "sub_category_name"=>$this->faker->name,
           'product_study_category_id'=>$category->id,
        ];
    }
}
