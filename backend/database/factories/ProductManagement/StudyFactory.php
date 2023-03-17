<?php

namespace Database\Factories\ProductManagement;

use App\Models\Company;
use App\Models\ProductManagement\Product;
use App\Models\ProductManagement\StudyModel;
use App\Models\ProductManagement\StudyPurpose;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::inRandomOrder()->first();
        $company = Company::inRandomOrder()->first();
        $study_pupose = StudyPurpose::inRandomOrder()->first();
        $study_model = StudyModel::inRandomOrder()->first();
        return [
            "studiable_type" => Product::class,
            "studiable_id" => $product->id,
            "study_language" => $this->faker->randomElement(['en', 'ar', 'fa']),
            "status" => $this->faker->randomElement(['pending', 'reject', 'in_study', 'in_study_ reivew', 'study_reject', 'complete', 'in_hold', 'cancel', 'delete']),
            "work_priority" => $this->faker->randomElement(['high', 'medium', 'low']),
            "order_note" => $this->faker->text(100),
            "study_recommendations" => $this->faker->text(100),
            "target_gender" => $this->faker->randomElement(['male', 'female', 'all']),
            "start_target_age" => rand(0, 100),
            "end_target_age" => rand(0, 100),
            "target_note" => $this->faker->text(100),
            "company_id" => $company->id,
            "study_purpose_id" => $study_pupose->id,
            "study_model_id" => $study_model->id
        ];
    }
}
