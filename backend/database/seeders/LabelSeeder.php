<?php

namespace Database\Seeders;

use App\Models\Label;
use App\Models\LabelCategory;
use App\Models\SubSystem;

use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cat = ['Bad comments', 'Best selling ads', 'most wanted ads', 'Bad ads'];


        foreach ($cat as $key => $value) {
            # code...
            LabelCategory::create([
                "title" => $value
            ]);
        }
        $labels = Label::factory(1)->create();
        $sub_systems = SubSystem::query()->inRandomOrder()->take(1);
        foreach ($labels as $label) {
            $label->subSystems()->sync($sub_systems->pluck("id")->toArray());
        }
    }
}
