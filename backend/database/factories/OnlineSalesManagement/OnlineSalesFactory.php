<?php

namespace Database\Factories\OnlineSalesManagement;

use App\Models\Advertisement\Project;
use App\Models\Company;
use App\Models\Country;
use App\Models\OnlineSalesManagement\OnlineSales;
use Illuminate\Database\Eloquent\Factories\Factory;



class OnlineSalesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $i=1;
        return [
            "code"      => rand(100000, 9999999999),
            "product_name"     => $this->faker->unique->name,
            "product_name_arabic"     => $this->faker->unique->name,
            "product_code"     => "SF".$i++,
            "status"    => 'new_sales',
            "sales_type"    => $this->faker->randomElement(['Landing Page Sales','WhatsApp Sales','Quick Card Sales']),
            'country_id' => Country::inRandomOrder()->first()->id,
            'company_id' => Company::inRandomOrder()->first()->id,
            'project_id' => Project::inRandomOrder()->first()->id,
        ];
    }
}
