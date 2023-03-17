<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Action;
use App\Models\ProductManagement\Study;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyPreparationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $study = Study::inRandomOrder()->first();
        $action = Action::inrandomOrder()->first();
        return [
            "prepartation_type" => $this->faker->randomElement(['landig_page', 'text', 'file']),
            "preparationt_content" => $this->faker->text(300),
            "status" => $this->faker->randomElement(['content_filled', 'no_content']),
            "study_id" => $study->id,
            "action_id" => $action->id
        ];
    }
}
