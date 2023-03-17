<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\Ipa;
use App\Models\SingleSales\IpaAddPlatform;
use App\Models\SocialMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class IpaPlatformTargetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ipa=Ipa::inRandomOrder()->first();
        $social=IpaAddPlatform::inRandomOrder()->first();
        return [
        'ipa_id'=>$ipa->id,
        'add_platform_id'=>$social->id,
        'target_gender'=>$this->faker->randomElement(['Male', 'Female',"all"]), 
        'start_target_age'=>rand(18,25),
        'end_target_age'=>rand(25,55), 
        'budget_type'=>$this->faker->randomElement(['unlimited','daily', "weekly",'monthly']), 
        'budget'=>rand(102,1222.3),
        'target_cost_per_order'=>rand(102,1222.3)
        ];
    }
}
 
