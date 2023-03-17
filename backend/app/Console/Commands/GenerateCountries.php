<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Language;
use App\Models\State;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GenerateCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate All Countries';

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
     * @throws GuzzleException
     */
    public function handle()
    {

        DB::beginTransaction();
        try {
            $countryFile    = json_decode(File::get(resource_path() . "/json/countries+states+cities.json"));

            foreach ($countryFile as $key => $country) {
                $countryList = (array)$country;
                $countryModel = new Country();
                $countryModel = array_intersect_key($countryList, array_flip($countryModel->getFillable()));
                $countryModel["timezones"] = json_encode($countryModel["timezones"]);
                $countryModel["translations"] = json_encode((array)$countryModel["translations"]);

                list($national, $languages) = $this->getNationalAndLanguage($countryModel["iso2"]);
                $countryModel["national"] = $national;
                $countryModel["code"] = rand(100000, 9999999999);
                $countryModel["status"] = "active";
                $insertedCountry = Country::create($countryModel);
                foreach ($languages as $language) {
                    // $language["country_id"] = $insertedCountry->id;
                    $lang = Language::firstOrCreate($language);
                    $lang->countries()->attach($insertedCountry->id);
                }
                foreach ($country->states as $stateKey => $state) {
                    $stateModel = new State();
                    $stateList = (array)$state;
                    $stateModel = array_intersect_key($stateList, array_flip($stateModel->getFillable()));
                    $stateModel["country_id"] = $insertedCountry->id;
                    State::create($stateModel);
                }
            }
            DB::commit();
            $this->info("All Countries Generated Successfully!");
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->info($exception);
            $this->info("Something went wrong!");
            $this->error("Please try again!");
        }
        return 1;
    }


    private function getNationalAndLanguage($code2)
    {
        $countryNationalFile = json_decode(File::get(resource_path() . "/json/CountriesAndStates.json"));
        $countryLanguageFile = json_decode(File::get(resource_path() . "/json/languages.json"));


        $national = '';
        $languages = array();

        if (is_array($countryNationalFile)) {
            foreach ($countryNationalFile as $countryNational) {
                if ($countryNational->code2 === $code2) {
                    $national = $countryNational->nationality;
                    $languageCodes = $countryNational->languages;
                    if (is_array($countryLanguageFile)) {
                        foreach ($languageCodes as $languageCode) {
                            foreach ($countryLanguageFile as $language) {
                                if ($language->code === $languageCode) {
                                    $language = (array) $language;
                                    $languageItem["name"] = $language['name'];
                                    $languageItem["native"] = $language['native'];
                                    $languageItem["code"] = $language['code'];
                                    $languageItem["dir"] = array_key_exists('rtl', $language) ? 'rtl' : 'ltr';
                                    $languages[] = $languageItem;
                                }
                            }
                        }
                    }
                }
            }
        }
        return array($national, $languages);
    }
}
