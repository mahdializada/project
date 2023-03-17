<?php

namespace Database\Seeders\SingleSales;

 
use App\Models\SingleSales\StudyPurpose;
use Illuminate\Database\Seeder;

class StudyPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        StudyPurpose::factory(10)->create();
    }
}
