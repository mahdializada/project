<?php

namespace App\Console\Commands;

use App\Models\Industry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GenerateIndustries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:industries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate some predefined industries';

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
        $industries = json_decode(File::get(resource_path() . "/json/industries.json"));
        foreach ($industries as $industry) {
            Industry::create(["name" => $industry]);
        }

        $currencyFile   = json_decode(File::get(resource_path() . "/json/currencies.json"));
        foreach($currencyFile as $key => $currency){
            DB::table('currencies')->insert([
                'symbol'            => $currency->symbol,
                'name'              => $currency->name,
                'symbol_native'     => $currency->symbol_native,
                'decimal_digits'    => $currency->decimal_digits,
                'rounding'          => $currency->rounding,
                'code'              => $currency->code,
                'name_plural'       => $currency->name_plural,
            ]);
        }



        return 0;
    }
}
