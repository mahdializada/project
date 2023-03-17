<?php

namespace Database\Factories\SingleSales;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;


class SourcingRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {


        $state = State::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();

        return [
            "code" => rand(100000, 9999999999),
            "sourcing_type" => $this->faker->randomElement(["quantity check (old)", "supplier check (new)"]),
            "required_shipping_method" => $this->faker->randomElement(["air", "sea", "land", "all"]),
            "status" => $this->faker->randomElement(["pending", "rejected", "in_sourcing", "in_hold", "sourcing_failed", "semi_found", "found", "canceled", "deleted"]),
            "state_id" => $state->id,
            "note" => $this->faker->unique->text,
            "created_by" => $user->id,
            
         

        ];
    }
}
