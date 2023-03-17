<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Action;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Action::factory(10)->create();
    }
}
