<?php

namespace Database\Factories\SingleSales;

use App\Models\Country;
use App\Models\SingleSales\Ipa;
use App\Models\SingleSales\Ispp;
use Illuminate\Database\Eloquent\Factories\Factory;

class TargetCountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tagetable=$this->faker->randomElement([  Ipa::class , Ispp::class ]);
        $tagetable_id =$tagetable::inRandomOrder()->first()->id;
        $country_id =Country::inRandomOrder()->first()->id;
        return [
            "targetable_type"=>$tagetable,
            "targetable_id"=>$tagetable_id,
            "country_id"=>$country_id,
          
            
        ];
    }
}
