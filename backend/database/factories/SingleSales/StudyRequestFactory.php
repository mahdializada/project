<?php

namespace Database\Factories\SingleSales;

use App\Models\User;
use App\Models\Language;
use App\Models\SingleSales\Product;
use App\Models\SingleSales\StudyRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();
        $lang = Language::inRandomOrder()->first();
        return [
            "code" => rand(100000, 9999999999),
            'name' => $this->faker->unique->name,
            "study_reason" => $this->faker->unique->text,
            "product_id" => $product->id,
            "study_language_id" => $lang->id,
            "created_by" => $user->id,
            "updated_by" => $user->id,
            "status" => StudyRequest::getTypes()[rand(0, 4)],
        ];
    }
}
