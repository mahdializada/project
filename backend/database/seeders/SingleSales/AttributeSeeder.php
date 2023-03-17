<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Attribute::factory(10)->create();
    }
}
