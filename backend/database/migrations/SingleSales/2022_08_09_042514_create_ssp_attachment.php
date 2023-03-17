<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSspAttachment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments_ssp', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->integer('size');
            $table->string('name');
            $table->string('path');
            $table->string('attachmentable_type');
            $table->string('attachmentable_id');
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
        Schema::dropIfExists('attachments_ssp');
    }
}
