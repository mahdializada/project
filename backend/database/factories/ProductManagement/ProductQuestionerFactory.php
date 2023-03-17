<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\ProductQuestion;
use App\Models\ProductManagement\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductQuestionerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $request = Request::inRandomOrder()->first();
        $question = ProductQuestion::inRandomOrder()->first();
        return [
            "ispp_request_id" => $request->id,
            "question_id" => $question->id,
            "answer" => rand(0, 1),
            "answer_details" => $this->faker->text(40)
        ];
    }
}
