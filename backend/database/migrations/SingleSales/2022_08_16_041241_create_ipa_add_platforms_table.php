<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpaAddPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipa_add_platforms', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("Platform_name");
            $table->boolean("is_active");

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
        Schema::dropIfExists('ipa_add_platforms');
    }
}
