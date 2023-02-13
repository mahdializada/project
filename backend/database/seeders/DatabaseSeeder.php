<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountrySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(BrancheSeeder::class);
        $this->call(SocialMediaSeeder::class);
        $this->call(CompanySocailMediaSeeder::class);
        $this->call(SystemSeeder::class);
        $this->call(CompanySystemSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(UserDepartmentSeeder::class);
        $this->call(TeamSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(TeamRoleSeeder::class);
        $this->call(TeamMemberSeeder::class);
        $this->call(DepartmentRoleSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(ColumnSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
