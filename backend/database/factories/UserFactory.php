<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->lastname(),
            'user_name' => $this->faker->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(),
            'change_password' =>$this->faker->password(),
            'phone' => $this->faker->phoneNumber(),
            'birth_date' => $this->faker->date(),
            'image' => $this->faker->image(),
            'gender' => $this->faker->randomElement(['male','female']),
            'permission_type' => $this->faker->randomElement(['role ','team','user']),
            'status' => $this->faker->randomElement(['pending ','active','inactive','blocked']),

        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
