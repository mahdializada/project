<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_text', function (Blueprint $table) {
            $table->id();
            $table->string('ad_textable_type');
            $table->string('ad_textable_id');
            $table->string('ad_text_name');
            $table->text('ad_text');
            $table->string('ad_text_language');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_text');
    }
}
