<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmIpaAdPlacemnetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_ipa_ad_placemnets', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("ipa_request_id")->references("id")->on("pdm_ipa_requests");
            $table->foreignUuid("ad_placement_id")->references("id")->on("pdm_ad_placemnets");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_ipa_ad_placemnets');
    }
}
