<?php

namespace Database\Seeders\Landing\Product;

use Database\Seeders\Landing\Product\Master\CategorySeeder as ProductCategorySeeder;
use Database\Seeders\Landing\Product\Master\SubCategorySeeder as ProductSubCategorySeeder;
use Database\Seeders\Landing\Product\Master\Requirement\CategorySeeder as RequirementCategorySeeder;
use Database\Seeders\Landing\Product\Master\Requirement\SubCategorySeeder as RequirementSubCategorySeeder;
use Database\Seeders\Landing\Product\Master\Skill\CategorySeeder as SkillCategorySeeder;
use Database\Seeders\Landing\Product\Master\Skill\SubCategorySeeder as SkillSubCategorySeeder;
use Database\Seeders\Landing\Product\Master\Worker\CategorySeeder as WorkerCategorySeeder;
use Database\Seeders\Landing\Product\Master\Worker\SubCategorySeeder as WorkerSubCategorySeeder;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProductCategorySeeder::class);
        $this->call(ProductSubCategorySeeder::class);
        $this->call(RequirementCategorySeeder::class);
        $this->call(RequirementSubCategorySeeder::class);
        $this->call(SkillCategorySeeder::class);
        $this->call(SkillSubCategorySeeder::class);
        $this->call(WorkerCategorySeeder::class);
        $this->call(WorkerSubCategorySeeder::class);
    }
}
