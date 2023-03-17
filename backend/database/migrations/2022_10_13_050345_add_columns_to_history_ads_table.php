<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToHistoryAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_ads', function (Blueprint $table) {
            //
            $table->timestamp('crm_refresh_date')->nullable();
            $table->timestamp('tikrok_refresh_date')->nullable();
            $table->timestamp('facebook_refresh_date')->nullable();
            $table->timestamp('google_ads_refresh_date')->nullable();
            $table->timestamp('snapchat_refresh_date')->nullable();
            $table->double("exact_price")->default(0.00);
            $table->double("sale_price")->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('history_ads', function (Blueprint $table) {
            //
        });
    }
}
