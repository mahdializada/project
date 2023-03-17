<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Study;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferenceFactory extends Factory
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
            "refrence_name"  => $this->faker->name,
            "refrence_description" => $this->faker->text(100),
            "study_id" => $study->id,
        ];
    }
}
