<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\IsppQuestioner as IsppQuestioners;
use Illuminate\Database\Seeder;

class IsppQuestionersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IsppQuestioners::factory(10)->create();
    }
}
