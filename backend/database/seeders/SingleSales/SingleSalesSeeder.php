<?php

namespace Database\Seeders\SingleSales;

use App\Models\SingleSales\IpaPlatformTargeting;

use Database\Seeders\SingleSales\SalesChanelSeeder;
use Illuminate\Database\Seeder;

class SingleSalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(AttributeSeeder::class);
        $this->call(CategorySeeder::class);
        // $this->call(AttributeCategorySeeder::class);
        $this->call(SupplierSeeder::class);
        // $this->call(BrandSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(StudyPurposeSeeder::class);
        $this->call(IsppSeeder::class);
        $this->call(IpaSeeder::class);
        $this->call(ProductStudySeeder::class);
        $this->call(ProductStudyResultSeeder::class);
        $this->call(ProductInventorySeeder::class);

        // $this->call(ProductAttributeSeeder::class);

        $this->call(SellingGoalsSeeder::class);
        $this->call(QuantityReservationRequestSeeder::class);
        $this->call(SourcingRequestSeeder::class);
        $this->call(SourcingRequestProductSeeder::class);
        $this->call(SourcingRequestResultSeeder::class);


        // $this->call(ActionSeeder::class);
        $this->call(ActionClassSeeder::class);
        $this->call(StudyRequestSeeder::class);
        $this->call(IsppTargetSaleCountrySeeder::class);

        $this->call(QuestionSeeder::class);

        $this->call(ProductStudyEvaluationSeeder::class);
        $this->call(ProductStudyModelSeeder::class);
        $this->call(InterestValueSeeder::class);

        $this->call(IsppQuestionersSeeder::class);
        $this->call(ProductSourcSeeder::class);
        $this->call(ProductSourcValueSeeder::class);
        $this->call(ProductStudyFormComponentSeeder::class);
        $this->call(ProductStudyFormFulfillmentSeeder::class);
        $this->call(SalesChanelSeeder::class);
        $this->call(IpaAddPlatformSeeder::class);
        $this->call(IpaAddPlacementSeeder::class);
        $this->call(IpaAddPlacementSeeder::class);
        $this->call(IpaPlatformTargetingSeeder::class);
        $this->call(ActionClassSeeder::class);
        $this->call(TargetCountrySeeder::class);
    }
}
