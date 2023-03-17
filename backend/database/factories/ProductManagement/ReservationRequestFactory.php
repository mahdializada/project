<?php

namespace Database\Factories\ProductManagement;

use App\Models\Company;
use App\Models\Reason;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $company = Company::inRandomOrder()->first();
        $city = State::inRandomOrder()->first();
        $reason = Reason::inRandomOrder()->first();
        return [
            "is_group" => rand(0, 1),
            "status" => $this->faker->randomElement(['pending', 'rejected', 'in_process', 'in_hold', 'not_possible', 'order_made', 'semi_purchased', 'recieved_in_warehouse', 'canceled',  'deleted']),
            "products_note" => $this->faker->text(200),
            "shipping_note" => $this->faker->text(200),
            "shipping_mehtod" => $this->faker->randomElement(['air', 'land', 'sea']),
            "company_id" => $company->id,
            "importing_city_id" => $city->id,
            "reason_id" => $reason->id
        ];
    }
}
