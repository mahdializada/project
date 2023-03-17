<?php

namespace App\Console\Commands;

use App\Models\Company;
use Carbon\Carbon;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use App\Models\Language;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:super_admin';

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
        DB::beginTransaction();
        try {
            User::query()->where("email", "admin@admin.com")->forceDelete();
            $country = Country::inRandomOrder()->first();
            $state = State::inRandomOrder()->first();
            $language = Language::query()->inRandomOrder()->first();
            $user = User::create([
                'firstname' => 'Admin',
                "lastname" => 'Admin',
                "username" => 'admin@admin',
                'email' => 'admin@admin.com',
                'password' => "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", // password
                "phone" => '0799999999',
                "whatsapp" => '0799999999',
                "note" => '',
                "change_password" => false,
                "schedule_type" => "unlimited",
                "time_range" => null,
                "date_range" => null,
                "code" => rand(100000, 9999999999),
                "gender" => 'male',
                "status" => "active",
                "birth_date" => Carbon::now()->subYears(20),
                "tracing_status" => true,
                "image" => "https://picsum.photos/500/300?image=60",
                "address" => 'address',
                "country_id" => $country->id,
                "state_id" => $state->id,
                "current_country_id" => $country->id,
                "language_id" => $language->id,
                "functional" => true,
            ]);
            $companies = Company::all()->toArray();
            $companies = array_column($companies, 'id');
            $user->companies()->attach($companies);
            $permissions = DB::select('select * from action_sub_system');
            $permissions = array_map(function ($value) {
                return ((array)$value)['id'];
            }, $permissions);
            $user->permissions()->attach($permissions);
            $repo = new UserRepository();
            $repo->storeDefaultStorage($user);
            DB::commit();
            $this->info("Super Admin Created Successfully!");
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->info($exception);
            $this->info("Something went wrong!");
        }
        return 1;
    }
}
