<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductList;

class ProductListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductList::factory(20)->create();
    }
}
