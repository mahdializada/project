<?php

namespace Database\Seeders;

use App\Models\Advertisement\Application;
use App\Models\Advertisement\Connection;
use App\Models\Advertisement\Platform;
use App\Models\Advertisement\PlatformName;
use App\Models\Advertisement\Project;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::factory(30)->create();
        $plats = array(
            [
                'code' => rand(1000, 99999999),
                'name' => 'facebook'
            ],
            [
                'code' => rand(1000, 99999999),
                'name' => 'snapchat'
            ],
            [
                'code' => rand(1000, 99999999),
                'name' => 'tiktok'
            ],
            [
                'code' => rand(1000, 99999999),
                'name' => 'google ads'
            ],
            [
                'code' => rand(1000, 99999999),
                'name' => 'google analytics'
            ],
        );
        foreach ($plats as $value) {
            PlatformName::create($value);
            $platform =   Platform::create([
                "platform_name" => $value["name"],
                "platform_key" => $value["name"],
                "code" => rand(1000, 999999999)
            ]);

            $companies = Company::all();
            $platform->companies()->sync($companies);
        }
    }
}
