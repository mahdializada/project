<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdAccountPixelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_account_pixels', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("pixel_name")->nullable();
            $table->string("pixel_id");
            $table->string("pixel_code")->nullable();
            $table->string("organization_id")->nullable();
            $table->string("ad_account_id");
            $table->string("status")->nullable();
            $table->text("pixel_script")->nullable();
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
        Schema::dropIfExists('ad_account_pixels');
    }
}
