<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloudinaryTempFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloudinary_temp_files', function (Blueprint $table) {
            $table->id();
            $table->string('locale_path')->nullable();
            $table->string('cloud_path')->nullable();
            $table->string('public_id')->nullable();
            $table->string('file_type')->nullable();
            $table->string('name');
            $table->integer('size');
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
        Schema::dropIfExists('cloudinary_temp_files');
    }
}
