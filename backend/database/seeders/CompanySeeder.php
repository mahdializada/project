<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Country;
use App\Models\System;
use Illuminate\Database\Seeder;
use App\Models\Folder;
use App\Models\User;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $custom_countries = ['UAE', 'Kuwait', 'Qatar', 'Iraq'];
        $custom_companies = ['Oredoh', 'Uaeshopix', 'Flaminstore', 'Fairyshoping', 'Teebalhoor'];

        foreach ($custom_countries as $country_name) {
            foreach ($custom_companies as $name) {
                $company =   Company::create([
                    "code" => rand(100000, 9999999999),
                    "name" => $country_name . "-" . $name,
                    "logo" => 'companies/logos/' . $name . '.png',
                    "email" => $country_name . "-" . $name . "@gmail.com",
                    "investment_type" => "Main Company",
                    "status" => "active",
                    "note" => 'company note',
                    "created_by" => User::inRandomOrder()->first()->id,
                    "updated_by" => User::inRandomOrder()->first()->id
                ]);
                $systems = System::all();
                $company->systems()->sync($systems);
                $temp_name = $country_name == "UAE" ? "United Arab Emirates" : $country_name;
                $company->countries()->sync(Country::whereName($temp_name)->pluck("id")->toArray());
            }
        }


        // $companies = Company::factory(1)->create();
        // foreach ($companies as $company) {
        //     $countries = Country::query()->inRandomOrder()->take(2);
        //     $systems = System::all();
        //     $company->systems()->sync($systems);
        //     foreach ($company->systems as $companySystems) {
        //         foreach ($companySystems->subSystems as $subSystem) {
        //             if ($subSystem->has_file) {
        //                 $folder = Folder::firstOrCreate([
        //                     'name' => $subSystem->name,
        //                     'company_id' => $company->id,
        //                     'sub_system_id' => $subSystem->id,
        //                 ]);
        //                 if ($folder->name == 'Design Request') {
        //                     Folder::create([
        //                         'name' => 'Attachments',
        //                         'company_id' => $company->id,
        //                         'parent_id' => $folder->id,
        //                         'sub_system_id' => $subSystem->id,
        //                     ]);
        //                 }
        //                 if ($folder->name == 'Users') {
        //                     Folder::firstOrCreate([
        //                         'name' => 'Profiles',
        //                         'company_id' => $company->id,
        //                         'parent_id' => $folder->id,
        //                         'sub_system_id' => $subSystem->id,
        //                     ]);
        //                 }
        //                 if ($folder->name == 'Companies') {
        //                     Folder::firstOrCreate([
        //                         'name' => 'Company Logos',
        //                         'company_id' => $company->id,
        //                         'parent_id' => $folder->id,
        //                         'sub_system_id' => $subSystem->id,
        //                     ]);
        //                 }
        //             }
        //         }
        //     }
        //     $company->countries()->sync($countries->pluck("id")->toArray());
        // }
    }
}
