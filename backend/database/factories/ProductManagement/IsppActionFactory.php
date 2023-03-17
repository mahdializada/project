<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Action;
use App\Models\ProductManagement\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

class IsppActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $action = Action::inRandomOrder()->first();
        $request = Request::inRandomOrder()->first();
        return [
            "ispp_note" => $this->faker->text(100),
            "status" => $this->faker->randomElement(['done', 'not_done']),
            "action_id" => $action->id,
            "ispp_request_id" => $request->id
        ];
    }
}
