<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DesignRequestOrder;

class DesignRequestOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DesignRequestOrder::factory(5)->create();
    }
}
