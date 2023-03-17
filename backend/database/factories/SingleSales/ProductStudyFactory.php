<?php

namespace Database\Factories\SingleSales;

use App\Models\Company;
use App\Models\CountryLanguage;
use App\Models\Language;
use App\Models\SingleSales\Ipa;
use App\Models\SingleSales\Ispp;
use App\Models\SingleSales\StudyPurpose;
use App\Models\SingleSales\Product;
use App\Models\SingleSales\ProductStudy; 
 
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

class ProductStudyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();
        $lang = Language::inRandomOrder()->first();
        $company=Company::inRandomOrder()->first();
        $purpose=StudyPurpose::inRandomOrder()->first();
        $gender=['Male',"Female",'All'];
        $studyable=$this->faker->randomElement([  Ipa::class , Ispp::class ]);
        $studyable_id =$studyable::inRandomOrder()->first()->id;
        return [
            "code" => rand(100000, 9999999999),
            'company_id' => $company->id,
            'studyable_id'=>$studyable_id,
            'studyable_type'=>$studyable,
            "study_purpose_id" => $purpose->id,
            'study_language_id'=>$lang->id,
            'work_priority'=>ProductStudy::getWorkPriority()[rand(0, 2)],
            'order_note'=>$this->faker->text,
            'study_recommendations'=>$this->faker->text,
            'target_gender'=>$gender[rand(0,2)],
            'start_target_age'=>rand(17,25),
            'end_target_age'=>rand(25,40),
            'target_note_name'=>$this->faker->unique->name,
            'progressive'=>rand(0,100),
            "status" => ProductStudy::getTypes()[rand(0, 6)],  
            "created_by" => $user->id,
            "updated_by" => $user->id,
           
        ];
    }
}
