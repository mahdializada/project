<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\InterestValue;
use Illuminate\Database\Seeder;

class InterestValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      InterestValue::factory(50)->create();
    }
}
