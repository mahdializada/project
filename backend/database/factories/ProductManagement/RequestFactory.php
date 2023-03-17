<?php

namespace Database\Factories\ProductManagement;

use App\Models\Company;
use App\Models\ProductManagement\Currency;
use App\Models\ProductManagement\SourceValue;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $company = Company::inRandomOrder()->first();
        $product_source_value = SourceValue::inRandomOrder()->first();
        $currency = Currency::inRandomOrder()->first();
        return [
            "working_type" => $this->faker->randomElement(['creation', 'competition']),
            "work_priority" => $this->faker->randomElement(['high', 'medium', 'low']),
            "is_group" => rand(0, 1),
            "products_availability" => $this->faker->randomElement(['available_in_stock', 'available_with_seller']),
            "available_quantity" => rand(1, 1000008),
            "is_product_seasonal" => rand(0, 1),
            "season_or_event_name" => $this->faker->randomElement(['spring', 'summer', 'fall', 'winter']),
            "individual_sale_note" =>  $this->faker->text(100),
            "products_note" => $this->faker->text(100),
            "display_languages" => ['ar', 'fa'],
            "store_channel_note" => $this->faker->text(100),
            "head_selling_prize" => rand(1, 2000),
            "financial_info_note" => $this->faker->text(100),
            "status" => $this->faker->randomElement(['pending', 'rejected', 'in_sale_module', 'sale_module_review', 'sale_model_reject', 'in_study', 'study_review', 'study_reject', 'in_content_creaion', 'content_creation_review', 'content_creation_review', 'completed', 'in_hold', 'canceled', 'deleted']),
            "company_id" => $company->id,
            "product_source_value_id" => $product_source_value->id,
            "currency_id" => $currency->id,
        ];
    }
}
