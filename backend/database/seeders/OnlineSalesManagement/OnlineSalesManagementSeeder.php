<?php

namespace Database\Seeders\OnlineSalesManagement;
use Illuminate\Database\Seeder;

class OnlineSalesManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(OnlineSalesSeeder::class);
    }
}
