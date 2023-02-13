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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('email');
            $table->string('logo');
            $table->enum('investment_type',['main company','others']);
            $table->enum('status',['pending ','active','inactive','blocked','removed'])->default('pending');
            $table->enum('pre_status',['pending ','active','inactive','blocked','removed'])->nullable();
            $table->foreignId('location_id')->nullable()->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('companies');
    }
};
