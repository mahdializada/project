<?php

namespace Database\Factories\SingleSales;

use App\Models\Company;
use App\Models\Country;
use App\Models\Language;
use App\Models\SingleSales\Brand;
use App\Models\SingleSales\Category;
use App\Models\SingleSales\Ispp;
use App\Models\SingleSales\Product;
use App\Models\SingleSales\ProductStudy;
use App\Models\SingleSales\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IsppTargetSaleCountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ispp = Ispp::inRandomOrder()->first();
        $country = Country::inRandomOrder()->first();

        return [
            "ispp_id" => $ispp->id,
            "country_id" => $country->id,
        ];
    }
}
