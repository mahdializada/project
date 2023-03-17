<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Study;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyEvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $study = Study::inRandomOrder()->first();
        return [
            "rating" => rand(0, 5),
            "comment" => $this->faker->text(50),
            "study_id" => $study->id
        ];
    }
}
