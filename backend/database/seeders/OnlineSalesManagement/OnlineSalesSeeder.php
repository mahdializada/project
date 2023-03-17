<?php

namespace Database\Seeders\OnlineSalesManagement;

use App\Models\OnlineSalesManagement\OnlineSales;
use Illuminate\Database\Seeder;

class OnlineSalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        OnlineSales::factory(1)->create();
    }
}
