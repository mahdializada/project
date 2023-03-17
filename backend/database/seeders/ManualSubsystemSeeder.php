<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\System;
use App\Models\SubAction;
use App\Models\SubSystem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ManualSubsystemSeeder extends Seeder
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:systems';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert All Systems';

    /**
     * Create a new command instance.
     *
     * @return void
     */


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $systemsFile = json_decode(File::get(resource_path() . "/json/systems.json"));
            $actionFile = json_decode(File::get(resource_path() . "/json/actions.json"));
            foreach ($systemsFile as $key => $system) {
                $system = (array)$system;
                $systemModel = System::firstOrCreate(['name' => $system['name'], 'phrase' => $system['phrase'], 'logo' => $system['logo']]);

                foreach ((array)$system['sub_systems'] as $key => $sub_system) {
                    $sub_system = (array)$sub_system;
                    $subSystemModel = SubSystem::firstOrCreate(['name' => $sub_system['name'], 'scope' => $sub_system['scope'], 'phrase' => $sub_system['phrase'], 'system_id' => $systemModel->id, 'has_status' => $sub_system['has_status'], 'has_file' => $sub_system['has_file']]);
                    foreach ((array)$sub_system['actions'] as $key => $action) {
                        $action = (array)$action;
                        $actionModel = new Action();
                        $actionCreated = $actionModel->firstOrCreate(['name' => $action['name']], ['phrase' => $action['phrase']],);
                        $subSystemModel->actions()->attach($actionCreated);
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
        }
    }
}
