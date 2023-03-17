<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\FormComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormFulfillmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $form_component = FormComponent::inRandomOrder()->first();
        return [
            "form_component_id" => $form_component->id,
            "study_form_answer" => $this->faker->text(100)
        ];
    }
}
