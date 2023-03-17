<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmLandingPageUrlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_landing_page_url', function (Blueprint $table) {
            $table->id();
            $table->string('landing_page_urlable_type');
            $table->string('landing_page_urlable_id');
            $table->string('landing_page_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdm_landing_page_url');
    }
}
