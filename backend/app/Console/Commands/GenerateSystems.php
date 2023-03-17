<?php

namespace App\Console\Commands;

use App\Models\Action;
use App\Models\System;
use App\Models\SubAction;
use App\Models\SubSystem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GenerateSystems extends Command
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
                $systemModel = new System();
                $systemCreated = $systemModel->create(['name' => $system['name'], 'phrase' => $system['phrase'], 'logo' => $system['logo']]);
                $this->info($system['name'] . ' Successfully Created!');
                foreach ((array)$system['sub_systems'] as $key => $sub_system) {
                    $sub_system = (array)$sub_system;
                    $subSystemModel = new SubSystem();
                    $subSystemCreated = $subSystemModel->firstOrCreate(['name' => $sub_system['name'], 'scope' => $sub_system['scope'], 'phrase' => $sub_system['phrase'], 'system_id' => $systemCreated->id, 'has_status' => $sub_system['has_status'], 'has_file' => $sub_system['has_file']]);
                    $this->info($sub_system['name'] . 'Sub System Successfully Created!');
                    foreach ((array)$sub_system['actions'] as $key => $action) {
                        $action = (array)$action;
                        $actionModel = new Action();
                        $actionCreated = $actionModel->firstOrCreate(['name' => $action['name']], ['phrase' => $action['phrase']],);
                        $subSystemCreated->actions()->attach($actionCreated);
                        $this->info($action['name'] . ' Action Successfully Created!');
                        $arr = [];
                        foreach ($actionFile as $key => $actionF) {
                            $actionF = (array)$actionF;
                            if ($actionF['name'] == $actionCreated->name) {
                                foreach ((array)$actionF['sub_actions']  as $key => $subAction) {
                                    $subAction = (array)$subAction;
                                    $subActionModel = new SubAction();
                                    $subActionCreated = $subActionModel->firstOrCreate(['name' => $subAction['name'], 'action' => $subAction['action']]);
                                    array_push($arr, $subActionCreated->id);
                                    $this->info(
                                        'Action ' .
                                            $action['name'] . ' with  Sub Action ' .
                                            $subActionCreated['name'] . ' On Sub System ' .  $subSystemCreated['name'] . ' Relation Successfully Created!'
                                    );
                                }
                            }
                        }
                        $actionCreated->subActions()->sync($arr);
                    }
                }
            }
            DB::commit();
            $this->info("All Systems Inserted Successfully!");
        } catch (\Exception $exception) {

            DB::rollBack();
            $this->info($exception);
            $this->info("Something went wrong!");
            $this->error("Please try again!");
        }
        return 1;
    }
}
