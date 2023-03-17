<?php

namespace Database\Seeders\SingleSales;

use App\Models\Company;
use App\Models\Language;
use App\Models\Currency;
use App\Models\SingleSales\Ispp;
use Illuminate\Database\Seeder;

class IsppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $companies = Company::get()->toArray();
        $currency = Currency::inRandomOrder()->first();
        $language = Language::inRandomOrder()->first();
        foreach ($companies as $company) {
            # code...
            $data = [
                "code" => rand(100000, 9999999999),
                "company_id" => $company['id'],
                "working_type" => 'competation company',
                "product_type" => " single product",
                "priority" => "high",
                "product_availablity" => 'high',
                "season_or_event_name" => 'winter',
                "available_quantity" => rand(001, 10000),
                "sale_note" => 'salse note',
                "product_note" => 'product note',
                "chanel_note" => 'chanel note',
                "financial_note" => 'financial note',
                "Currency_id" => $currency->id,
                "display_language_id" => $language->id,
                "status" => "completed",
            ];
            Ispp::create($data);
        }
    }
}
