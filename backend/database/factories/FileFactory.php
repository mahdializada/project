<?php

namespace Database\Factories;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->unique->name,
            "mime_type" => $this->faker->name,
            "path" => "https://picsum.photos/id/11/10/6",
            "thumbnail_path" => "https://picsum.photos/id/11/10/7",
            "size" =>  rand(1000,500000),
            "created_by" => User::inRandomOrder()->first()->id, 
            "description" => $this->faker->text,
             
        ];
    }
}
