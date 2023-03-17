<?php

namespace Database\Factories\SingleSales;

use App\Models\Company;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class IsppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $company = Company::inRandomOrder()->first();
        $currency = Currency::inRandomOrder()->first();
        $language = Language::inRandomOrder()->first();
        return [
            "code" => rand(100000, 9999999999),
            "company_id" => $company->id,
            "working_type" => $this->faker->randomElement(['creation', 'competation company']),
            "product_type" => $this->faker->randomElement(['single product', 'group product']),
            "priority" => $this->faker->randomElement(['high', 'medium', 'low']),
            "product_availablity" => $this->faker->randomElement(['high', 'medium', 'low']),
            "season_or_event_name" => $this->faker->name,
            "available_quantity" => rand(001, 10000),
            "sale_note" => $this->faker->text,
            "product_note" => $this->faker->text,
            "chanel_note" => $this->faker->text,
            "financial_note" => $this->faker->text,
            "currency_id" => $currency->id,
            "display_language_id" => $language->id,
            "status" => "completed",
        ];
    }
}
