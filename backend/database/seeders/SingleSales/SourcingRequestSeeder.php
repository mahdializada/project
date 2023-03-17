<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\SourcingRequest;
use Illuminate\Database\Seeder;

class SourcingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        SourcingRequest::factory(10)->create();
    }
}
