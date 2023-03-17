<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryLanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 2021_12_21_071250_create_languages_table

        Schema::create('country_language', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('country_id')->nullable()->constrained()->cascadeOnUpdate()->nullable()->cascadeOnDelete();
            $table->foreignUuid('language_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('country_language');
    }
}
