<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\StudyModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormComponentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $study_model = StudyModel::inRandomOrder()->first();
        return [
            "type" => $this->faker->randomElement(['short_text', 'long_text']),
            "type_code" => $this->faker->text(200),
            "label_name" => $this->faker->name,
            "study_model_id" => $study_model->id
        ];
    }
}
