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
        Schema::create('company_social_media', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('socialMedia_id')->unsigned();
            $table->bigInteger('company_id')->unsigned();
            $table->string('link');
            $table->enum('status',['pending ','active','inactive','blocked'])->default('pending');
            $table->foreign('socialMedia_id')->references('id')->on('social_medias')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('company_social_media');
    }
};
