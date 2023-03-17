<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\Landing\LandingSeeder;
use Database\Seeders\OnlineSalesManagement\OnlineSalesManagementSeeder;
use Database\Seeders\SingleSales\SingleSalesSeeder;
use Database\Seeders\ProductManagement\PdmSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        // first db wipe
        $databaseName = DB::getDatabaseName();
        $tables = DB::select("SELECT * FROM information_schema.tables WHERE table_schema = '$databaseName'");
        foreach ($tables as $table) {
            $name = $table->TABLE_NAME;
            //if you don't want to truncate migrations
            if ($name == 'migrations') {
                continue;
            }
            DB::table($name)->truncate();
        }

        Schema::enableForeignKeyConstraints();
        Artisan::call("create:countries");
        Artisan::call("create:systems");
        Artisan::call("create:industries");
        $this->call(LandingSeeder::class);
        $this->call(LabelSeeder::class);
        Artisan::call("create:super_admin");
        $this->call(ReasonSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(FileSeeder::class);
        Artisan::call("insert:translations");
        $this->call(NoteSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(BusinessLocationSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(TeamSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(CompanyUserSeeder::class);
        Artisan::call("passport:install");
        Artisan::call("passport:keys --length=2048 --force");
        Artisan::call("insert:socialmedia");
        Artisan::call("insert:notifications");
        $this->call(MetaDataSeeder::class);
        $this->call(DesignRequestSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(SingleSalesSeeder::class);
        $this->call(AdvertisementSeeder::class);
        $this->call(SubSystemColumnSeeder::class);
        // $this->call(OnlineSalesManagementSeeder::class);
        // $this->call(PdmSeeder::class);
    }
}
