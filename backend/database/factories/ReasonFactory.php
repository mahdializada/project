<?php

namespace Database\Factories;
use App\Models\System;


use Illuminate\Database\Eloquent\Factories\Factory;

class ReasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $system = System::inRandomOrder()->first();
        return [
            "code" => rand(100000, 9999999999),
            "system_id" => $system->id, 
            "title" => $this->faker->unique->name,
        ];
    }
}
