<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Ipa;
use Illuminate\Database\Seeder;

class IpaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Ipa::factory(10)->create();
    }
}
