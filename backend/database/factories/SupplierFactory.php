<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $user = User::inRandomOrder()->first();
        return [
            'code'                      => uniqueCode(Supplier::class, "TALS"),
            'supplier_trading_name'     => $this->faker->name,
            'supplier_name'             => $this->faker->name,
            'email'                     => $this->faker->email,
            'main_phone'                => $this->faker->unique->e164PhoneNumber(),
            'purchase_order_phone'      => $this->faker->unique->e164PhoneNumber(),
            'website'                   => $this->faker->url,
            'prepration_period'         => $this->faker->name,
            'supply_type'               => $this->faker->name,
            'commercial_type'           => $this->faker->name,
            'legal_type'                => $this->faker->name,
            'country_type'              => $this->faker->name,
            // 'sourcer'                   =>$this->faker->randomElement(['Main Office', 'Khalil']),
            'note'                      => $this->faker->paragraph(),
            "created_by" => $user->id,
            "updated_by" => $user->id,
        ];
    }
}
