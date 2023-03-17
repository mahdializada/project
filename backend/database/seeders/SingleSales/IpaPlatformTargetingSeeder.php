<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\IpaPlatformTargeting;
use Illuminate\Database\Seeder;

class IpaPlatformTargetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IpaPlatformTargeting::factory(20)->create();
    }
}
