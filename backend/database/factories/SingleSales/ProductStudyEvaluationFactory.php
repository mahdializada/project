<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\ProductStudy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStudyEvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $study = ProductStudy::inRandomOrder()->first();
        return [
      
            "product_study_id" => $study->id,
            "user_id"=>$user->id,
            "rating" =>rand(1, 100),
            "comment" => $this->faker->text,
        ];
    }
}
