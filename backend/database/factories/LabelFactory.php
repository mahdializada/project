<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LabelCategory;
use App\Models\SubSystem;



class LabelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $colors         = ['#1976D2FF', '#9719D2FF', '#C01667FF', '#0A27EEFF'];
        $statuses       = ['archive', 'live'];
        return [
            "code"      => rand(100000, 9999999999),
            "title"     => $this->faker->unique->name,
            "label"     => $this->faker->name,
            "status"    => 'live',
            'label_category_id' => LabelCategory::inRandomOrder()->first()->id,
            "color"     => $colors[rand(0, 3)]
        ];
    }
}
