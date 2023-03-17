<?php

namespace Database\Factories;

use App\Models\BusinessLocation;
use App\Models\User;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types  = Department::getTypes();
        $business_location = BusinessLocation::where('status', 'active')->inRandomOrder()->first();
        return [
            "name" => "Main Department",
            "note" => $this->faker->unique->text,
            "code" => rand(100000, 999999999999),
            "status" => "active", //$this->faker->randomElement($types),
            "business_location_id" => $business_location->id,
            "created_by" => User::inRandomOrder()->first()->id,
            "updated_by" => User::inRandomOrder()->first()->id,
        ];
    }
}
