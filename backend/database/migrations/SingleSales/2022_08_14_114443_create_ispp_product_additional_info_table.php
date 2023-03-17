<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsppProductAdditionalInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ispp_product_additional_info', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ispp_id')->constrained("ispp_ssp")->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum("type", ["description", "product content and advertisement reference", "financial informationa and suggestion", "competitor advertiser reference"])->nullable();
            $table->text("reference_note")->nullable();
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
        Schema::dropIfExists('ispp_product_additional_info');
    }
}
