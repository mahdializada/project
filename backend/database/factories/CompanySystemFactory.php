<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\System;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanySystem>
 */
class CompanySystemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $system=System::inRandomOrder()->first()->id;
        $company=Company::inRandomOrder()->first()->id;
        return [
            'system_id'=>$system,
            'company_id'=>$company,
        ];
    }
}
