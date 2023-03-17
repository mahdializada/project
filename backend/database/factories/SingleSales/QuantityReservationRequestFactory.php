<?php

namespace Database\Factories\SingleSales;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuantityReservationRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $state = State::inRandomOrder()->first();


        return [
            "code" =>  rand(100000, 9999999999),
            "products_note" =>  $this->faker->unique->text,
            "shipping_method" =>  $this->faker->randomElement(['air', 'sea', 'ground', 'other']),
            "shipping_note" => $this->faker->unique->text,
            "status" => $this->faker->randomElement(['pending', 'rejected', 'in process', 'not possible', 'order made', 'order received', 'cancelled']),
            'importing_state_id'=>$state->id
        ];
    }
}
