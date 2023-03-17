<?php

namespace Database\Factories\SingleSales;;

use App\Models\SingleSales\Product;
use App\Models\SingleSales\QuantityReservationRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuantityReservationProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::inRandomOrder()->first();
        $quantity = QuantityReservationRequest::inRandomOrder()->first();
        return [
           "quantity"=>$this->faker->randomNumber(),
           "status"=>$this->faker->randomElement(['in process', 'order made', "received in warehouse"]),
           'purchase_order_number'=>$this->faker->unique->text,
           "product_id"=>$product->id,
           "quantity_reservation_id"=>$quantity->id,
            'arrival_time'=>null,
            'purchase_date'=>null,
        ];
    }
}
