<?php

namespace Database\Factories\SingleSales;
use App\Models\SingleSales\SourcingRequestProduct;
use App\Models\SingleSales\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SourcingResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {


        $sourcing_request_product = SourcingRequestProduct::inRandomOrder()->first();
        $supplier = Supplier::inRandomOrder()->first();

        return [
            "code" => rand(100000, 9999999999),
            "sourcing_request_product_id" => $sourcing_request_product->id,
            "cost" => rand(40, 1000),
            "supplier_id" => $supplier->id,
            "shipping_method" => $this->faker->randomElement(['air', 'sea', 'ground', "other"]),
            "shipping_cost" =>  $this->faker->unique->name,
            "delivery_time" => $this->faker->unique->date,
            "available_quantities" => rand(1000, 5000),
            "import_restrictions" => $this->faker->unique->text,
            "quantity_model" => $this->faker->randomElement(['whole sale', 'retail']),
            "sourcing_note" => $this->faker->unique->text,
            "product_note" => $this->faker->unique->text,
        
        ];
    }
}
