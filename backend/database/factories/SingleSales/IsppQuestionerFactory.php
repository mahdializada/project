<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\Ispp;
use App\Models\SingleSales\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class IsppQuestionerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ispp = Ispp::inRandomOrder()->first();
        $question = Question::inRandomOrder()->first();

        return [
            "ispp_id" => $ispp->id,
            "question_id" => $question->id,
            "answer" => $this->faker->boolean(),
            "answer_details" => $this->faker->unique->text
        ];
    }
}
