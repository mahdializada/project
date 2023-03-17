<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;



class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($index = 0; $index < 20; $index++) {
            $supplier = Supplier::factory(1)->create();
            // foreach ($supplier as $sup) {
            //     $companies = Company::query()->inRandomOrder()->take(3);
            //     $sourcers = Sourcer::query()->inRandomOrder()->take(3);
            //     $sup->companies()->sync($companies->pluck("id")->toArray());
            //     $sup->sourcers()->sync($sourcers->pluck("id")->toArray());
            // }
        }
    }
}
