<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\SocialMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanySocialMedia>
 */
class CompanySocialMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $socialMedia=SocialMedia::inRandomOrder()->first()->id;
        $comany=Company::inRandomOrder()->first()->id;
        return [
            'socialMedia_id'=>$socialMedia,
            'company_id'=>$comany,
            'link'=>$this->faker->url(),
            'status' => $this->faker->randomElement(['pending ','active','inactive','blocked']),
        ];
    }
}
