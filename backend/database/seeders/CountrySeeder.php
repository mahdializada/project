<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countryFile    = json_decode(File::get(resource_path() . "/json/country.json"));

        foreach ($countryFile as $key => $country) {
            $countryList = (array) $country;
            $countryModel = new Country();
            $countryModel->iso2 = $countryList["iso2"];
            $countryModel->name = $countryList["name"];
            $countryModel->capital = $countryList["capital"];
            $countryModel->native = $countryList["native"];
            $countryModel->phone_code = $countryList["phone_code"];
            $countryModel->currency_name = $countryList["currency_name"];
            $countryModel->currency_symbol = $countryList["currency_symbol"];
            $countryModel->region = $countryList["region"];
            $countryModel->subregion = $countryList["subregion"];
            $countryModel->timezones = $countryList["timezones"][0]->zoneName;
            $countryModel->status = "active";
            $countryModel->save();

    }
}
}