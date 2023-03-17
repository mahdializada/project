<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\ProductStudyFormComponent;
use App\Models\SingleSales\ProductStudyModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStudyFormComponentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $form=ProductStudyModel::inRandomOrder()->first();
        return [
            "type"=>ProductStudyFormComponent::getType()[rand(0, 1)],
            "type_code"=>$this->faker->text,
            "label_name"=>$this->faker->name,
            'product_study_model_id'=>$form->id,
        ];
    }
}
