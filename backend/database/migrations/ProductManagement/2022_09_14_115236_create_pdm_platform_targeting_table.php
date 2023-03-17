<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmPlatformTargetingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_platform_targeting', function (Blueprint $table) {
            $table->id();
            $table->enum('target_gender', ['male', 'female', 'all']);
            $table->integer('start_target_age');
            $table->integer('end_target_age');
            $table->string('target_note');
            $table->enum('budget_type', ['limited', 'unlimited']);
            $table->enum('budget_time_type', ['daily', 'weekly', 'monthly']);
            $table->double('budget');
            $table->double('target_cost_per_order');

            $table->foreignUuid("platform_id")->references("id")->on("pdm_ad_platforms");
            $table->foreignUuid("ipa_request_id")->references("id")->on("pdm_ipa_requests");
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
        Schema::dropIfExists('pdm_platform_targeting');
    }
}
