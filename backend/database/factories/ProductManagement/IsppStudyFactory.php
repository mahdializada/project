<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\Request;
use App\Models\ProductManagement\Study;
use Illuminate\Database\Eloquent\Factories\Factory;

class IsppStudyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $study = Study::inRandomOrder()->first();
        $request = Request::inRandomOrder()->first();
        return [
            "study_id" => $study->id,
            "ispp_request_id" => $request->id,
            "study_note" => $this->faker->text(100)
        ];
    }
}
