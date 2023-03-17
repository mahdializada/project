<?php

namespace Database\Factories\Advertisement;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Advertisement\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name(),
            "code"=> rand(1000, 99999999),
            "client_id" => $this->faker->text(),
            "client_secret" => $this->faker->text(),
            "access_token" => $this->faker->text(),
            "refresh_token" => $this->faker->text(),
            "expires_in" => Carbon::now(),
            "domain" => $this->faker->domainName(),
            "platform_id" => Platform::inRandomOrder()->first()->id,
            "updated_by" => User::inRandomOrder()->first()->id,
            "created_by" => User::inRandomOrder()->first()->id,
        ];
    }
}
