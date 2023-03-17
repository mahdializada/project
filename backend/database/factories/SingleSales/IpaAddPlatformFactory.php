<?php

namespace Database\Factories\SingleSales;

use Illuminate\Database\Eloquent\Factories\Factory;

class IpaAddPlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'Platform_name'=>$this->faker->unique->randomElement(['Facebook','Instagram', "LinkedIn",'Whatsapp','Telegram','Twitter','Youtube','Zoom','Imo']), 
        'is_active'=>rand(0,1),
        ];
    }
}
