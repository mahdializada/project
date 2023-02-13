<?php

namespace Database\Seeders;

use App\Models\CompanySocialMedia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySocailMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompanySocialMedia::factory(10)->create();
    }
}
