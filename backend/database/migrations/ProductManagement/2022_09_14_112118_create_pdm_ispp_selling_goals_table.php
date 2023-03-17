<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmIsppSellingGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_ispp_selling_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("ispp_request_id")->references("id")->on("pdm_ispp_requests");
            $table->foreignUuid("goal_id")->references("id")->on("pdm_selling_goals");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_ispp_selling_goals');
    }
}
