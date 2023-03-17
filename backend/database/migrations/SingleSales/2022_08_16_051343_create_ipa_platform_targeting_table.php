<?php

use App\Models\SingleSales\IpaPlatformTargeting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpaPlatformTargetingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipa_platform_targeting', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid('ipa_id')->constrained('ipa_ssp')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('add_platform_id')->constrained('ipa_add_platforms')->cascadeOnUpdate()->cascadeOnDelete();

            $table->string("target_gender");
            $table->integer("start_target_age")->nullable();
            $table->integer("end_target_age")->nullable();
            $table->text("target_note")->nullable();
            $table->enum("budget_type",IpaPlatformTargeting::getBudgetType())->nullable();
            $table->double('budget');
            $table->double('target_cost_per_order');

            $table->timestamps();
        });
    }
 
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ipa_platform_targeting');
    }
}
