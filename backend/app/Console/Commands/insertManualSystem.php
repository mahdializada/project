<?php

namespace App\Console\Commands;

use App\Models\Action;
use App\Models\SubAction;
use App\Models\SubSystem;
use App\Models\System;
use Database\Seeders\SubSystemColumnSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class insertManualSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:new-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'operation done';

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
            $systemsFile = json_decode(File::get(resource_path() . "/json/systems.json"));
            $actionFile = json_decode(File::get(resource_path() . "/json/actions.json"));
            foreach ($systemsFile as $key => $system) {
                $system = (array)$system;
                // $systemModel = System::firstOrCreate(['name' => $system['name'], 'phrase' => $system['phrase'], 'logo' => $system['logo']]);
                $systemModel = System::where(['name' => $system['name'], 'phrase' => $system['phrase']])->first();
                if (!$systemModel) {


                    $systemModel = System::firstOrCreate(['name' => $system['name'], 'phrase' => $system['phrase'], 'logo' => $system['logo']]);
                }
                foreach ((array)$system['sub_systems'] as $key => $sub_system) {
                    $sub_system = (array)$sub_system;

                    // 'scope' => $sub_system['scope'],
                    $subSystemModel = SubSystem::where(['name' => $sub_system['name'],  'phrase' => $sub_system['phrase']])->first();
                    if (!$subSystemModel) {
                        $subSystemModel = SubSystem::create(['name' => $sub_system['name'], 'scope' => $sub_system['scope'], 'phrase' => $sub_system['phrase'], 'system_id' => $systemModel->id, 'has_status' => $sub_system['has_status'], 'has_file' => $sub_system['has_file']]);
                        $this->info($sub_system['name'] . " subsystem inserted!");
                    }
                    foreach ((array)$sub_system['actions'] as $key => $action) {
                        $action = (array)$action;
                        $actionCreated = Action::firstOrCreate(['name' => $action['name'], 'phrase' => $action['phrase']]);
                        $subSystemModel->actions()->syncWithoutDetaching($actionCreated);
                        $arr = [];
                        foreach ($actionFile as $key => $actionF) {
                            $actionF = (array)$actionF;
                            if ($actionF['name'] == $actionCreated->name) {
                                foreach ((array)$actionF['sub_actions']  as $key => $subAction) {
                                    $subAction = (array)$subAction;
                                    $subActionModel = new SubAction();
                                    $subActionCreated = $subActionModel->firstOrCreate(['name' => $subAction['name'], 'action' => $subAction['action']]);
                                    array_push($arr, $subActionCreated->id);
                                }
                            }
                        }
                        $actionCreated->subActions()->sync($arr);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $exception) {

            DB::rollBack();
            $this->info($exception->getMessage());
        }
        $this->info("
        System has been inserted!");
        return 1;
    }
}
