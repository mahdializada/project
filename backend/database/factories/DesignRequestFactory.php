<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Company;
use App\Models\DesignRequest;
use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

class DesignRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $companyId = Company::inRandomOrder()->first()->id;
        $userId = User::inRandomOrder()->first()->id;
        return [
            "type" => DesignRequest::getTypes()[rand(0, 4)],
            "product_code" => $this->faker->name(),
            "product_name" => $this->faker->name(),
            "template_id" => Template::inRandomOrder()->first()->id,
            "company_id" => $companyId,
            "updated_by" => $userId,
            "created_by" => $userId,
        ];
    }
}
