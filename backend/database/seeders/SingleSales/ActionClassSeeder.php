<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\ActionClass;
use Illuminate\Database\Seeder;

class ActionClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        ActionClass::factory(10)->create();
    }
}
