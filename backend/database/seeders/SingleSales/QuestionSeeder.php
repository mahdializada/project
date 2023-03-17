<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\Question as Questionar;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Questionar::factory(10)->create();
    }
}
