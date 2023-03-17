<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Template;
use App\Models\DesignRequest;
use App\Models\DesignRequestOrder;
use App\Models\StatusTimeConsumed;
use Illuminate\Database\Seeder;

class DesignRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = User::inRandomOrder()->first()->id;
        Template::create(["name" => "Fredy", 'type' => 'landing page design']);
        Template::create(["name" => "Oredo", 'type' => 'landing page design']);
        foreach (DesignRequest::getTypes() as $value) {
            Template::create([
                'name' => $value . " Template",
                "type" => $value,
            ]);
        }
        DesignRequest::factory(5)->create();
        DesignRequestOrder::factory(50)->create();
    }
}
