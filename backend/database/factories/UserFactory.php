<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use App\Models\Language;
use App\Models\National;
use App\Models\TranslatedLanguage;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $country = Country::inRandomOrder()->first();
        $state = State::inRandomOrder()->first();
        $language = Language::query()->inRandomOrder()->first();
        return [
            'firstname' => $this->faker->unique->firstName,
            "lastname" => $this->faker->unique->lastName,
            "username" => $this->faker->unique->userName,
            'email' => $this->faker->unique->email,
            'password' => "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", // password
            "phone" => $this->faker->unique->e164PhoneNumber(),
            "whatsapp" => $this->faker->unique->e164PhoneNumber(),
            "note" => $this->faker->unique->text,
            "change_password" => false,
            "schedule_type" => "unlimited",
            "time_range" => null,
            "date_range" => null,
            "code" => rand(100000, 9999999999),
            "gender" => $this->faker->randomElement(['male', 'female']),
            "status" => $this->faker->randomElement(["active", "inactive", "blocked", "pending", "warning"]),
            "birth_date" => Carbon::now()->subYears(20),
            "tracing_status" => true,
            "image" => $this->faker->unique->imageUrl,
            "address" => $this->faker->address,
            "country_id" => $country->id,
            "state_id" => $state->id,
            "current_country_id" => $country->id,
            "language_id" => $language->id,
        ];
    }
}
