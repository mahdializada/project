<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\Category;
use App\Models\SingleSales\ProductStudySubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStudyModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    { 
        $category= Category::inRandomOrder()->first();
        return [
           "study_model_name"=>$this->faker->name,
           'study_sub_category_id'=>$category->id,
        ];
    }
}
