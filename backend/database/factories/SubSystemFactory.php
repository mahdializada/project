<?php

namespace Database\Factories;

use App\Models\System;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubSystemFactory extends Factory
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
            "name" => $this->faker->unique->name,
            "system_id" => $system->id,
        ];
    }
}
