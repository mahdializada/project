<?php

namespace Database\Seeders;

use App\Models\Advertisement\ColumnCategory;
use App\Models\Advertisement\SubSystemTab;
use App\Models\Advertisement\TabColumn;
use App\Models\Company;
use App\Models\SubSystem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubSystemColumnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $files = [
        //     "advertisement", "business_locations", 'social_media',
        //     'countries', 'companies', "departments", 'languages',
        //     "labels", "status_events",  "reasons", "users",
        //     "teams", "roles", "user_activity", "storage_requests",
        //     'design_request', 'my_orders', "products", "sourcing_request",
        //     "ipa", "quantity_reservation_request", "categories", "attributes",
        //     "ispp", "actions", "Brands", "ProductStudy", "platforms", "projects", "column_category",
        //     "applications","manual_upload"
        // ];
        $files = [
            "online_sales","advertisement", "business_locations", 'social_media',
            'countries', 'companies', "departments", 'languages',
            "labels", "status_events",  "reasons", "users",
            "teams", "roles", "user_activity",
            "ipa",
            "ispp", "actions",  "platforms", "product_list", "projects", "column_category",
            "applications", "manual_upload",
            "Brands","products","categories","attributes","ProductStudy","my_orders_p",
            "quantity_reservation_request","sourcers"
        ];
        // 'currency'
        // ,"history_CMS"

        Schema::disableForeignKeyConstraints();
        DB::table('sub_system_tabs')->truncate();
        DB::table('tab_columns')->truncate();
        DB::table('column_categories')->truncate();
        DB::table('column_category_sub_systems')->truncate();
        Schema::enableForeignKeyConstraints();



        foreach ($files as $subsystem_phrase) {
            # code...
            if ($subsystem_phrase == "advertisement") {
                $default_visible = false;
            } else {

                $default_visible = true;
            }


            $filePath = resource_path("json/column_setting/$subsystem_phrase.json");
            if ($subsystem_phrase == "status_events") {
                $subsystem_id = SubSystem::where('name', 'Status Events (master)')->first();
            } else if ($subsystem_phrase == "reasons") {
                $subsystem_id = SubSystem::where('name', "=", 'Reasons (master)')->first();
            } else if ($subsystem_phrase == "labels") {
                $subsystem_id = SubSystem::where('name', 'Labels (master)')->first();
            } else {
                $subsystem_id = SubSystem::where("phrase", $subsystem_phrase)->first();
            }

            $subsystem_id = $subsystem_id["id"];
            if (file_exists($filePath)) {
                $systems = file_get_contents($filePath); //data read from json file
                $system  = json_decode($systems);
                // dd($system);
                foreach ($system as $key => $tabs) {
                    $tab = SubSystemTab::create(['tab_name' => $subsystem_phrase . "_" . $key, "sub_system_id" => $subsystem_id]);
                    foreach ($system->$key as $columns) {
                        TabColumn::create([
                            "tab_id" => $tab->id,
                            "text" => $columns->text,
                            "value" => $columns->value,
                            "visible" => $columns->visible ?? $default_visible,
                            "tooltip" => $columns->tooltip ?? '',
                            "sortable" => $columns->sortable ?? false
                        ]);


                    }
                }
            }
        }
        $categories = ["General Information", 'Date', 'CRM', "Google Analytics", "Calculation", "Platform"];
        $user =   User::inRandomOrder()->first();
        $subsystem = SubSystem::where("phrase", "advertisement")->first();
        foreach ($categories as $category) {
            $category =  ColumnCategory::create([
                'code' => rand(10000, 10000000),
                "category_name" => $category,
                'created_by' => $user['id']
            ]);
            $category->subSystems()->syncWithoutDetaching([$subsystem->id]);
        }
    }
}
