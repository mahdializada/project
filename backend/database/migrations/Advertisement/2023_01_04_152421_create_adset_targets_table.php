<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsetTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adset_targets', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("adset_id")->index();
            $table->json("age")->nullable();
            $table->string("gender")->nullable();
            $table->json("location")->nullable();
            $table->json("language")->nullable();
            $table->json("connection_type")->nullable();
            $table->json("device_model")->nullable();
            $table->json("operation_system")->nullable();
            $table->json("operating_system_version")->nullable();
            $table->json("interest")->nullable();
            $table->boolean("targeting_expansion")->default(false);
            $table->boolean("auto_targeting")->default(false);

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
        Schema::dropIfExists('adset_targets');
    }
}
