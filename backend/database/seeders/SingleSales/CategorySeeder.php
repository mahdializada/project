<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Category as SingleSalesCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        SingleSalesCategory::factory(100)->create();
        $categories = SingleSalesCategory::get();
        foreach ($categories as $cat) {
            $categroy = SingleSalesCategory::inRandomOrder()->first();
            $cat->parent_id = rand(0, 1) ? $categroy->id : null;
            $cat->save();
        }
    }
}
