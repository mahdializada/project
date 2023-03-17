<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            $companies = Company::query()->inRandomOrder()->take(1);
            $teams = Team::query()->inRandomOrder()->take(1);
            $roles = Role::query()->inRandomOrder()->take(1);
            $user->companies()->sync($companies->pluck("id")->toArray());
            $user->roles()->sync($roles->pluck("id")->toArray());
            $user->teams()->sync($teams->pluck("id")->toArray());
        }
    }
}
