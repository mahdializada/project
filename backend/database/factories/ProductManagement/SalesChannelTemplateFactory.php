<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\SalesChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesChannelTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sales_channel = SalesChannel::inRandomOrder()->first();
        return [
            "template" => $this->faker->text(50),
            "sales_channel_id" => $sales_channel->id
        ];
    }
}
