<?php

namespace Database\Factories\ProductManagement;

use App\Models\Company;
use App\Models\Reason;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class SourcingOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $reason = Reason::inRandomOrder()->first();
        $company = Company::inRandomOrder()->first();
        $city = State::inRandomOrder()->first();
        return [
            "sourcing_type" =>  $this->faker->randomElement(
                ['quantity_check', 'supplier_check']
            ),
            "is_group" => rand(0, 1),
            "required_shipping_method" => $this->faker->randomElement(
                ['air', 'land', 'sea']
            ),
            "status" => $this->faker->randomElement(
                ['pending', 'rejected', 'in_sourcing', 'in_hold', 'sourcing_failed', 'semi_found', 'found', 'canceled', 'deleted']
            ),
            "description" => $this->faker->text,
            "progressive" => rand(0, 100),
            "searching_reason_id" => $reason->id,
            "company_id" => $company->id,
            "importing_city" => $city->id,
        ];
    }
}
