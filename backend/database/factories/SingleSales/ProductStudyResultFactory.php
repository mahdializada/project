<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\ProductStudy;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStudyResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $productstudy = ProductStudy::inRandomOrder()->first();
        // protected $fillable = ['id', 'code', 'features', 'specification', 'strength_point', 'status', 'usages', 'target_audience', 'benefits', 'weaknesses', 'opportunities',
        //  'threats', 'study_notes', 'seller_information', 'product_study_id'];
        return [
            "code" =>  rand(100000, 9999999999),
            "features" =>  json_encode($this->faker->unique->name),
            'specification' => json_encode($this->faker->unique->name),
            'strength_point' => json_encode($this->faker->unique->name),
            'usages' => json_encode($this->faker->unique->name),
            'target_audience' => json_encode($this->faker->unique->name),
            'benefits' => json_encode($this->faker->unique->name),
            'weaknesses' => json_encode($this->faker->unique->name),
            'opportunities' => json_encode($this->faker->unique->name),
            'threats' => json_encode($this->faker->unique->name),
            'study_notes' => $this->faker->unique->text,
            'seller_information' => $this->faker->unique->text,
            'product_study_id' => $productstudy->id,
            "status" => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
