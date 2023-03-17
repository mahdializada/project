<?php

namespace Database\Factories\SingleSales;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $user = User::inRandomOrder()->first();
        return [
            //
            "name" => $this->faker->unique->name,
            "arabic_name" => $this->faker->unique->name,
            "type" => 'select_value',
            "code" => rand(100000, 9999999999),
            "values" => json_encode(['larg', 'small']),
            "created_by" => $user->id
        ];
    }
}
