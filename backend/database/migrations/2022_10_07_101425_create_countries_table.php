<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('iso2')->unique()->nullable();
            $table->string('name');
            $table->string('capital');
            $table->string('native')->nullable();
            $table->string('phone_code')->nullable();
            $table->string('currency_name');
            $table->string('currency_symbol');
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->enum('status',['active','inactive','blocked'])->default('active');
            $table->string('timezones')->nullable();
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
};
