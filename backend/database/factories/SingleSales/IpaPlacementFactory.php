<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\Ipa;
use App\Models\SingleSales\IpaAddPlacement;
use Illuminate\Database\Eloquent\Factories\Factory;

class IpaPlacementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
     
            $ipa=Ipa::inRandomOrder()->first();
            $placement=IpaAddPlacement::inRandomOrder()->first();
            return [
               "ipa_id"=>$ipa->id,
               "ipa_add_placement_id"=>$placement->id,
            ];
       
    }
}
