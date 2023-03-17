<?php

namespace Database\Seeders\Landing\Product\Master\Worker;

use App\Models\Landing\Master\Worker\WorkerCategory;
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
        WorkerCategory::factory(20)->create();
    }
}