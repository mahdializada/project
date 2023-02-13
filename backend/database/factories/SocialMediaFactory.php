<?php
namespace Database\Factories;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialMedia>
 */
class SocialMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user=User::inRandomOrder()->first()->id;
        return [
            'name'=>$this->faker->name(),
            'image'=>$this->faker->url(),
            'website'=>$this->faker->url(),
            'created_by'=>$user,
            'updated_by'=>$user,
        ];
    }
}
