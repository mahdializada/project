<?php

namespace Database\Factories\SingleSales;

use App\Models\File;
use App\Models\SingleSales\Category;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // protected $fillable = ['id', 'code', 'parent_sku', 'pcode', 'name', 'description', 'VAT_tax', 'unit', 'nubmer_of_sales', 'is_published', 'status', 'category_id', 'supplier_id', 'created_by'];


        $category = Category::inRandomOrder()->first();
        $supplier = Supplier::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        return [
            "name" => $this->faker->unique->name,
            "arabic_name" => $this->faker->unique->name,
            "parent_sku" => $this->faker->unique->name,
            "pcode" =>  rand(100000, 9999999999),
            "product_img" => File::inRandomOrder()->first()->id,
            "description" => $this->faker->unique->text,
            "arabic_description" => $this->faker->unique->text,
            // "VAT_tax" => rand(10, 999),
            // "unit" => 'meter',
            // "number_of_sales" => rand(0, 9999),
            "is_published" => rand(0, 1),
            "status" => $this->faker->randomElement(['active', 'inactive']),
            "category_id" => $category->id,
            "supplier_id" => $supplier->id,
            "created_by" => $user->id,

            "code" => rand(100000, 9999999999)
        ];
    }
}
