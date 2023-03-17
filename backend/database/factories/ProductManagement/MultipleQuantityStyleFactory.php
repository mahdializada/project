<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

class MultipleQuantityStyleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ispp_request = Request::inRandomOrder()->first();
        return [
            "ispp_request_id" => $ispp_request->id,
            "number_of_items" => rand(1, 100),
            "total_price" => rand(1, 10000)
        ];
    }
}
