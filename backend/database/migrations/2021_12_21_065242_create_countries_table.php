<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid("id")->primary();

            $table->string("name")->unique();
            $table->string("code")->unique();
            $table->enum("status", Country::getTypes())->default("active");
            $table->enum("advertisement_status", ['active', 'inactive'])->default('active');
            $table->string("iso2")->unique();
            $table->string("iso3")->unique();
            $table->string("numeric_code");
            $table->string("phone_code");
            $table->string("capital");
            $table->string("national");
            $table->string("currency");
            $table->string("currency_name");
            $table->string("currency_symbol");
            $table->string("tld");
            $table->string("native")->nullable();
            $table->string("region");
            $table->string("subregion");
            $table->json("timezones");
            $table->json("translations");
            $table->string("latitude")->nullable();
            $table->string("longitude")->nullable();
            $table->string("emoji");
            $table->string("emojiU");


            $table->softDeletes();
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
        Schema::dropIfExists('countries');
    }
}
