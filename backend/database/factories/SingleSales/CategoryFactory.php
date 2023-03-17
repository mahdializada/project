<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
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
            "name" => $this->faker->unique->name,
            "arabic_name" => $this->faker->name,
            "icon" => $this->faker->unique->imageUrl,
            // "banner" => $this->faker->unique->imageUrl,
            "description" => "Lorem ipsum dolor sit amet, consectetur adipisicing",
            "arabic_description" => "من المهم جدًا الاعتناء بالمريض ، وسيتم اتباع هذا المرض",
            "status" => "inactive",
            "code" => rand(100000, 9999999999),
            "parent_id" => null,
            "created_by" => $user->id,
            // "type" => $this->faker->randomElement(['product study','interest']),
        ];
    }
}
