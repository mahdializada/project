<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\DesignRequest;
use App\Models\DesignRequestOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class DesignRequestOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userId = User::inRandomOrder()->first()->id;
        $designRequestId = DesignRequest::inRandomOrder()->first()->id;
        if (DesignRequestOrder::where("design_request_id", $designRequestId)->exists()) {
            return;
        }

        return [
            "order_type" => "mix",
            "sales_note" => $this->faker->text(),
            "storyboard_note" => $this->faker->text(),
            "design_note" => $this->faker->text(),
            "design_link" => $this->faker->url(),
            "landing_page_link" => $this->faker->url(),
            "file_url" => $this->faker->url(),
            "updated_by" => $userId,
            "created_by" => $userId,
            "design_request_id" => $designRequestId,
        ];
    }
}
