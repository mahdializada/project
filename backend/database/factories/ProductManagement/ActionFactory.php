<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Product;
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
        $product = Product::inRandomOrder()->first();
        return [
            "actionable_type" => Product::class,
            "actionable_id" => $product->id,
            "work_priority" => $this->faker->randomElement(['high', 'medium', 'low']),
            "action_note" => $this->faker->text(100),
            "prograssive" => rand(0, 100),
            "status" => $this->faker->randomElement(['pending', 'in_progress', 'done', 'archived', 'failed', 'not_matching', 'cancel', 'delete', 'in_hold'])
        ];
    }
}
