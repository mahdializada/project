<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\State;
use App\Models\Folder;
use App\Models\System;
use App\Models\Company;
use App\Models\Country;
use App\Models\Industry;
use App\Models\Language;
use App\Models\Department;
use Illuminate\Console\Command;
use App\Models\BusinessLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class CreateSampleRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:sample-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        DB::beginTransaction();
        try {

            $this->info("Creating general records ...!");
            Artisan::call("create:countries");
            Artisan::call("create:systems");
            Artisan::call("create:industries");

            Artisan::call("passport:install");
            Artisan::call("passport:keys --length=512 --force");
            Artisan::call("insert:socialmedia");
            Artisan::call("insert:notifications");
            $this->info("Creating sample records ...!");

            $industry = Industry::inRandomOrder()->first();
            $country = Country::inRandomOrder()->first();
            $state = State::inRandomOrder()->first();
            $language = Language::query()->inRandomOrder()->first();

            $companies = [
                [
                    "code" => rand(100000, 9999999999),
                    "name" => "Teeb Al Hoor commercial Broker",
                    "logo" => 'https://api.oredoh.org/storage/companies/logos/2W5a4ObxpXZev7e262HJ8lZPWfIGhBole81fVyhN.jpg',
                    "email" => 'info@teebalhoor.com',
                    "investment_type" => "Main Company",
                    "status" => "active",
                    "note" => 'Teeb Al Hoor commerical Broker note goes here.',
                ],
                [
                    "code" => rand(100000, 9999999999),
                    "name" => "Oredoh",
                    "logo" => 'https://api.oredoh.org/storage/companies/logos/02MlgbGjo5ogCRatY0YhVJREJcUJGjFcRC74Ze52.png',
                    "email" => 'info@oredoh.org',
                    "investment_type" => "Main Company",
                    "status" => "active",
                    "note" => 'Oredoh note goes here.',
                ],
                [
                    "code" => rand(100000, 9999999999),
                    "name" => "Burwaz",
                    "logo" => 'https://api.oredoh.org/storage/companies/logos/NKh9krqQDfN6W9notCizV7C0ce5SiG4C49J1l3f0.png',
                    "email" => 'info@burwaz.com',
                    "investment_type" => "Main Company",
                    "status" => "active",
                    "note" => 'Burwaz note goes here.',
                ],
                [
                    "code" => rand(100000, 9999999999),
                    "name" => "Wings",
                    "logo" => 'https://api.oredoh.org/storage/companies/logos/hiMBp3vmf3rRmEgkht6i5JMEh7BlcLmLIf9wVGGh.png',
                    "email" => 'info@wings.com',
                    "investment_type" => "Main Company",
                    "status" => "active",
                    "note" => 'Wings note goes here.',
                ]
            ];

            for ($i = 0; $i < count($companies); $i++) {
                $company = Company::create($companies[$i]);
                foreach ($company->systems as $companySystems) {
                    foreach ($companySystems->subSystems as $subSystem) {
                        if ($subSystem->has_file) {
                            $folder = Folder::firstOrCreate([
                                'name' => $subSystem->name,
                                'company_id' => $company->id,
                                'sub_system_id' => $subSystem->id,
                            ]);
                        }
                    }
                }
            }
            $this->info("Companies has been created!");

            $company = Company::inRandomOrder()->first();
            $busniessLocations = [
                [
                    "name" => 'Kabul Office',
                    "status" => 'active',
                    "location_type" => 'head office',
                    "email" => 'sample@gmail.com',
                    "address" => 'Kabul, Afghanistan',
                    "note" => 'Location is not stable yet!',
                    "code" => '20192933',
                    "map_link" => 'http://www.oredoh.org',
                    "state_id" => $state->id,
                    "company_id" => $company->id,
                    "country_id" => $country->id
                ],
                [
                    "name" => 'Dubai Office',
                    "status" => 'active',
                    "location_type" => 'head office',
                    "email" => 'info@dubai.org',
                    "address" => 'UAE',
                    "note" => 'Location is not stable yet!',
                    "code" => '21192933',
                    "map_link" => 'http://www.burwaz.org',
                    "state_id" => $state->id,
                    "company_id" => $company->id,
                    "country_id" => $country->id
                ]
            ];

            for ($i = 0; $i < count($busniessLocations); $i++) {
                BusinessLocation::create($busniessLocations[$i]);
            }
            $this->info("Business Locations has been created!");


            $busniessLocation = BusinessLocation::inRandomOrder()->first();

            $departments = [
                [
                    "name" => 'Main Department',
                    "status" => 'active',
                    "code" => rand(100000, 999999999999),
                    "note" => 'Main department is really important.',
                    "business_location_id" => $busniessLocation->id,
                ],
                [
                    "name" => 'Call Center',
                    "status" => 'active',
                    "code" => rand(100000, 999999999999),
                    "note" => 'Call center department note goes here.',
                    "business_location_id" => $busniessLocation->id
                ]
            ];

            for ($i = 0; $i < count($departments); $i++) {
                $department = Department::create($departments[$i]);
                $industryIds = Industry::inRandomOrder()->take(5)->pluck('id');
                $companyIds = Company::inRandomOrder()->take(2)->pluck('id');
                $department->industries()->sync($industryIds);
                $department->companies()->sync($companyIds);
            }
            $this->info("Departments has been created!");

            $users = [
                [
                    'firstname' => 'Admin',
                    "lastname" => 'Admin',
                    "username" => 'Admin',
                    'email' => 'admin@admin.com',
                    'password' => "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", // password
                    "phone" => '009373627373',
                    "whatsapp" => '0093553627477',
                    "note" => 'Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).',
                    "change_password" => false,
                    "schedule_type" => "unlimited",
                    "time_range" => null,
                    "date_range" => null,
                    "code" => rand(100000, 9999999999),
                    "gender" => 'male',
                    "status" => 'active',
                    "birth_date" => '1997',
                    "tracing_status" => true,
                    "image" => 'https://cdn.vuetifyjs.com/images/cards/cooking.png',
                    "address" => 'Kabul, Afghanistan',
                    "country_id" => $country->id,
                    "state_id" => $state->id,
                    "current_country_id" => $country->id,
                    "language_id" => $language->id,
                ],
                [
                    'firstname' => 'Asadullah',
                    "lastname" => 'Moradi',
                    "username" => 'Asadullah',
                    'email' => 'asadullah@teebalhoor.com',
                    'password' => "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", // password
                    "phone" => '0093736273',
                    "whatsapp" => '00935536477',
                    "note" => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
                    "change_password" => false,
                    "schedule_type" => "unlimited",
                    "time_range" => null,
                    "date_range" => null,
                    "code" => rand(100000, 9999999999),
                    "gender" => 'male',
                    "status" => 'active',
                    "birth_date" => '1997',
                    "tracing_status" => true,
                    "image" => 'https://api.oredoh.org/storage/users/images/BiP5Sl43U3EMR9TKY5bPn2JPAQ3afRAH3w0F9v5V.jpg',
                    "address" => 'Kabul, Afghanistan',
                    "country_id" => $country->id,
                    "state_id" => $state->id,
                    "current_country_id" => $country->id,
                    "language_id" => $language->id,
                ]
            ];



            for ($i = 0; $i < count($users); $i++) {
                $user = User::create($users[$i]);

                $companies = Company::all()->toArray();
                $companies = array_column($companies, 'id');
                $user->companies()->attach($companies);
                $permissions = DB::select('select * from action_sub_system');
                $permissions = array_map(function ($value) {
                    return ((array)$value)['id'];
                }, $permissions);
                $user->permissions()->attach($permissions);
            }
            $this->info("Users has been created!");

            $this->info("Updating department relations...");
            $updatedDepartments = Department::get();
            $updatedDepartments->each(function ($department, $key) {
                $user = User::inRandomOrder()->first();
                $department->update(['updated_by' => $user->id, 'created_by' => $user->id]);
            });

            $this->info("Updating business location relations...");
            $updatedBusiness = BusinessLocation::get();
            $updatedBusiness->each(function ($busniessLocation, $key) {
                $user = User::inRandomOrder()->first();
                $busniessLocation->update(['updated_by' => $user->id, 'created_by' => $user->id]);
            });

            $this->info("Updating company relations...");
            $updatedCompanies = Company::get();
            $updatedCompanies->each(function ($company, $key) {
                $user = User::inRandomOrder()->first();
                $systems = System::inRandomOrder()->take(2)->pluck('id');
                $countries = Country::inRandomOrder()->take(2)->pluck('id');
                $company->systems()->sync($systems);
                $company->countries()->sync($countries);
                $company->update([
                    'updated_by' => $user->id,
                    'created_by' => $user->id
                ]);
            });

            $user = User::inRandomOrder()->first();
            $teams = [
                [
                    "code" => rand(100000, 9999999999),
                    "name" => 'Developers',
                    "number_of_people" => 2,
                    "note" => 'Coding is both love & life &#128525; !',
                    "schedule_type" => 'unlimited',
                    "created_by" => $user->id,
                    "updated_by" => $user->id,
                    "status" => 'active'
                ],
                [
                    "code" => rand(100000, 9999999999),
                    "name" => 'Designers',
                    "number_of_people" => 1,
                    "note" => 'Lets Design the world &#128151!',
                    "schedule_type" => 'unlimited',
                    "created_by" => $user->id,
                    "updated_by" => $user->id,
                    "status" => 'active'
                ]
            ];

            for ($i = 0; $i < count($teams); $i++) {
                $team = Team::create($teams[$i]);
                $departmentIds = Department::inRandomOrder()->take(1)->pluck('id');
                $userIds = User::inRandomOrder()->take(1)->pluck('id');
                $team->departments()->sync($departmentIds);
                $members = $team->members()->sync($userIds);
            }
            $this->info("Teams has been created!");

            $roles = [
                [
                    "code" => rand(100000, 9999999999),
                    "name" => 'Admin',
                    "number_of_people" => 2,
                    "note" => 'Admin is what every systems need.',
                    "schedule_type" => 'unlimited',
                    "created_by" => $user->id,
                    "updated_by" => $user->id,
                    "status" => 'active'
                ],
                [
                    "code" => rand(100000, 9999999999),
                    "name" => 'Manager',
                    "number_of_people" => 2,
                    "note" => 'Manage the system well, Please!',
                    "schedule_type" => 'unlimited',
                    "created_by" => $user->id,
                    "updated_by" => $user->id,
                    "status" => 'active'
                ]
            ];
            for ($i = 0; $i < count($roles); $i++) {
                $role = Role::create($roles[$i]);
                $departmentIds = Department::inRandomOrder()->take(1)->pluck('id');
                $role->departments()->sync($departmentIds);
            }
            $this->info("Roles has been created!");

            Artisan::call("insert:translations");
            $this->info("Translations has been inserted!");
    //        DB::commit();
        } catch (\Throwable $th) {
  //          DB::rollBack();
            $this->error('Something went wrong!         '. $th);
        }
      //  DB::rollBack();
        return 0;
    }
}
