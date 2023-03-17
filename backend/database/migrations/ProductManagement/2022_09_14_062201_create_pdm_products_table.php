<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("pdm_products", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("sku")->unique();
            $table->string("pcode")->unique()->nullable();
            $table->enum("type", ["phisycal", "digital"]);
            $table->string("name");
            $table->string("unit");
            $table->text("description");
            $table->double("vat_tax")->nullable();
            $table->enum("available", ["now", "comming_soon"]);
            $table->bigInteger('number_of_sales')->nullable();
            $table->boolean('is_published')->default(false);


            $table->foreignUuid("created_by")->references("id")->on("users");
            $table->foreignUuid("category_id")->references("id")->on("pdm_categories");
            $table->foreignUuid("brand_id")->references("id")->on("pdm_brands");
            // $table->foreignUuid("supplier_id")->references("id")->on("suppliers_ssp")->nullable();
            $table->uuid("supplier_id")->nullable();
            $table->foreign("supplier_id")->references("id")->on("suppliers_ssp");
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
        Schema::dropIfExists("pdm_products");
    }
}
