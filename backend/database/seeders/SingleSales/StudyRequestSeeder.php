<?php

namespace Database\Seeders\SingleSales;

use Illuminate\Database\Seeder;
use App\Models\SingleSales\StudyRequest;

class StudyRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StudyRequest::factory(10)->create();
    }
}
