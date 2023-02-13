<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**dat
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_medias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->string('website');
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status',['active','inactive','pending','blocked'])->default('pending');
            $table->enum('per_status',['active','inactive','pending','blocked'])->nullable();
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
        Schema::dropIfExists('social_media');
    }
};
