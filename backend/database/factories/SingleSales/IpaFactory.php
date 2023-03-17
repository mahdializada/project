<?php

namespace Database\Factories\SingleSales;

use App\Models\Company;
use App\Models\Country;
use App\Models\Language;
use App\Models\SingleSales\Brand;
use App\Models\SingleSales\Category;
use App\Models\SingleSales\Ipa;
use App\Models\SingleSales\Ispp;
use App\Models\SingleSales\Product;
use App\Models\SingleSales\ProductStudy;
use App\Models\SingleSales\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IpaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ispp = Ispp::inRandomOrder()->first();
       

        return [
            "code" => rand(100000, 9999999999), 
            "ispp_id" => $ispp->id,
            "ad_policy_violation" => Ipa::getPolicyViolation()[rand(0, 2)],
            "work_priority" => Ipa::getWorkPriority()[rand(0, 2)],
            "intensify_result_confirmation" => 1,
            "progressive" => rand(0,100), 
            "status" => Ipa::getStatus()[rand(0, 8)],
        ];
    }
}
