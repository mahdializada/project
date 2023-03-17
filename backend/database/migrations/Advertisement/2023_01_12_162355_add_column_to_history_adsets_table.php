<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToHistoryAdsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_adsets', function (Blueprint $table) {
            //
            $table->string('gender')->nullable();
            $table->json('age_groups')->nullable();
            $table->json('locations')->nullable();
            $table->json('languages')->nullable();
            $table->json('network_types')->nullable();
            $table->json('operating_systems')->nullable();
            $table->json('device_models')->nullable();
            $table->json('placements')->nullable();
            $table->string('placement_type')->nullable();
            $table->string('smart_targeting')->nullable();
            $table->string('promotion_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('history_adsets', function (Blueprint $table) {
            //
        });
    }
}
