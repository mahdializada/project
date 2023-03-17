<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ProductManagement\Action;
use App\Models\ProductManagement\AdPlacement;
use App\Models\ProductManagement\AdPlatform;
use App\Models\ProductManagement\Attribute;
use App\Models\ProductManagement\Brand;
use App\Models\ProductManagement\Category;
use App\Models\ProductManagement\Currency;
use App\Models\ProductManagement\FormComponent;
use App\Models\ProductManagement\FormFulfillment;
use App\Models\ProductManagement\FreeQuantityStyle;
use App\Models\ProductManagement\Instruction;
use App\Models\ProductManagement\Interest;
use App\Models\ProductManagement\InterestCategory;
use App\Models\ProductManagement\InterestValue;
use App\Models\ProductManagement\Inventory;
use App\Models\ProductManagement\IpaRequest;
use App\Models\ProductManagement\IsppAction;
use App\Models\ProductManagement\IsppStudy;
use App\Models\ProductManagement\MultipleQuantityStyle;
use App\Models\ProductManagement\PlatformTargeting;
use App\Models\ProductManagement\Product;
use App\Models\ProductManagement\ProductAdditionalInfo;
use App\Models\ProductManagement\ProductAttribute;
use App\Models\ProductManagement\ProductList;
use App\Models\ProductManagement\ProductPhoto;
use App\Models\ProductManagement\ProductQuestion;
use App\Models\ProductManagement\ProductQuestioner;
use App\Models\ProductManagement\ProductRequest;
use App\Models\ProductManagement\ProductSource;
use App\Models\ProductManagement\PurchasedProductInfo;
use App\Models\ProductManagement\Reference;
use App\Models\ProductManagement\Request;
use App\Models\ProductManagement\ReservationRequest;
use App\Models\ProductManagement\SalesChannel;
use App\Models\ProductManagement\SalesChannelTemplate;
use App\Models\ProductManagement\SellingGoal;
use App\Models\ProductManagement\SourceValue;
use App\Models\ProductManagement\SourcingOrder;
use App\Models\ProductManagement\SourcingOrderProduct;
use App\Models\ProductManagement\SourcingOrderResult;
use App\Models\ProductManagement\Study;
use App\Models\ProductManagement\StudyCategory;
use App\Models\ProductManagement\StudyEvaluation;
use App\Models\ProductManagement\StudyModel;
use App\Models\ProductManagement\StudyPreparation;
use App\Models\ProductManagement\StudyPurpose;
use App\Models\ProductManagement\StudySubCategory;
use Illuminate\Database\Seeder;

class PdmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::factory(50)->create();
        foreach ($categories as $category) {
            if (rand(0, 1)) {
                $parent = $categories[rand(0, $categories->count() - 1)];
                $category->parent_id = $parent->id;
                $category->save();
            }
        }
        Brand::factory(10)->create();
        Product::factory(10)->create();
        ProductPhoto::factory(100)->create();
        Inventory::factory(500)->create();
        Attribute::factory(20)->create();
        ProductAttribute::factory(500)->create();
        $orders = SourcingOrder::factory(50)->create();
        foreach ($orders as $order) {
            $countries = Country::inRandomOrder()->limit(4)->pluck('id');
            $order->countries()->sync($countries);
        }
        SourcingOrderProduct::factory(500)->create();
        SourcingOrderResult::factory(50)->create();
        ReservationRequest::factory(50)->create();
        ProductRequest::factory(50)->create();
        PurchasedProductInfo::factory(100)->create();
        StudyPurpose::factory(20)->create();
        StudyCategory::factory(10)->create();
        StudySubCategory::factory(20)->create();
        StudyModel::factory(40)->create();
        FormComponent::factory(10)->create();
        FormFulfillment::factory(100)->create();
        Study::factory(50)->create();
        StudyEvaluation::factory(50)->create();
        Reference::factory(40)->create();
        Action::factory(10)->create();
        $instructions = Instruction::factory(10)->create();
        foreach ($instructions as $instruction) {
            $actions = Action::inRandomOrder()->limit(3)->pluck('id');
            $instruction->actions()->sync($actions);
        }
        StudyPreparation::factory(20)->create();
        Currency::create(['name' => 'doller'], ['name' => 'درهم']);
        $productSources = ProductSource::factory(10)->create();
        foreach ($productSources as $productSource) {
            $temp = ProductSource::inRandomOrder()->first();
            $productSource->parent_id = $temp->id;
            $productSource->save();
        }
        SourceValue::factory(40)->create();
        $issp_requests = Request::factory(40)->create();
        SalesChannel::factory(20)->create();
        SalesChannelTemplate::factory(20)->create();
        SellingGoal::factory(50)->create();
        foreach ($issp_requests as $issp_request) {
            $temp = SalesChannelTemplate::inRandomOrder()->limit(2)->pluck('id');
            $temp2 = SellingGoal::inRandomOrder()->limit(5)->pluck('id');
            $issp_request->channelTemplates()->sync($temp);
            $issp_request->sellingGoals()->sync($temp2);
        }
        MultipleQuantityStyle::factory(10)->create();
        FreeQuantityStyle::factory(10)->create();
        ProductList::factory(30)->create();
        ProductAdditionalInfo::factory(20)->create();
        IsppAction::factory(30)->create();
        IsppStudy::factory(30)->create();
        ProductQuestion::factory(100)->create();
        ProductQuestioner::factory(200)->create();
        $ipa_requests = IpaRequest::factory(50)->create();
        AdPlatform::factory(20)->create();
        AdPlacement::factory(20)->create();
        foreach ($ipa_requests as $ipa_request) {
            $temp = AdPlacement::inRandomOrder()->limit(2)->pluck('id');
            $ipa_request->adPlacemnets()->sync($temp);
        }
        PlatformTargeting::factory(40)->create();
        InterestCategory::factory(10)->create();
        InterestValue::factory(50)->create();
        Interest::factory(50)->create();
    }
}
