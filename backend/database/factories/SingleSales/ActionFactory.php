<?php

namespace Database\Factories\SingleSales;

use App\Models\Company;
use App\Models\SingleSales\Ipa;
use App\Models\SingleSales\Ispp;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $company = Company::inRandomOrder()->first();
        $model = [Ispp::inRandomOrder()->first(), Ipa::inRandomOrder()->first()];
        return [
            "code" => rand(100000, 9999999999),
            "action_note" => $this->faker->text,
            "company_id" => $company->id,
            "actionable_type" => $this->faker->randomElement(['ispp', 'ipa_request']),
            "actionable_id" => $model[rand(0, 1)]->id,
            "work_priority" => $this->faker->randomElement(['high', 'low']),
            "status" => $this->faker->randomElement(['pending', 'in progress', 'done', 'archived', 'failed', 'not matching', 'cancelled', 'in hold']),
        ];
    }
}
