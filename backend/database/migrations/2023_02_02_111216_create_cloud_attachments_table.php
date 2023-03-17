<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloudAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloud_attachments', function (Blueprint $table) {
            $table->id();
            $table->integer('size')->nullable();
            $table->string('name')->nullable();
            $table->string('path')->nullable();
            $table->string('file_type')->nullable();
            $table->string('public_id')->nullable();
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
        Schema::dropIfExists('cloud_attachments');
    }
}
