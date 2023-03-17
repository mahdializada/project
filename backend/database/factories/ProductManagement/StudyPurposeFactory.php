<?php

namespace Database\Factories\ProductManagement;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudyPurposeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'study_purpose' => $this->faker->text(100),
        ];
    }
}
