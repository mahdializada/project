<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\IpaAddPlatform;
use Illuminate\Database\Seeder;

class IpaAddPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       IpaAddPlatform::factory(9)->create();
    }
}
