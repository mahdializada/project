<?php

namespace Database\Seeders\Landing\Product\Master\Worker;

use App\Models\Landing\Master\Worker\WorkerSubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WorkerSubCategory::factory(20)->create();
    }
}