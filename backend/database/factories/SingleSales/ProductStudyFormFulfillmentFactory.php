<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\ProductStudyFormComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStudyFormFulfillmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $formComponent=ProductStudyFormComponent::inRandomOrder()->first();
        return [
            'study_form_answer'=>$this->faker->text,
            'study_form_components_id'=>$formComponent->id,

        ];
    }
}
