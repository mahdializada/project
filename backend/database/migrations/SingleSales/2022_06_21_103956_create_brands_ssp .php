P<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsSsp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands_ssp', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('arabic_name');
            $table->boolean('is_approved')->default(0);
            $table->enum('status', ['active', 'inactive','banned'])->default('active');
            $table->foreignUuid('country_id')->nullable()->constrained()->cascadeOnUpdate()->nullable()->cascadeOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('brands_ssp');
    }
}