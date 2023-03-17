<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProfileInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_profile_infos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("code")->unique();
            $table->string("state");
            $table->string("item_code");
            $table->string("prod_sales_stability")->nullable();
            $table->string("prod_source");
            $table->string("prod_sale_goal");
            $table->string("prod_style");
            $table->string("prod_section");
            $table->json("prod_new_product_source");
            $table->string("prod_work_type");
            $table->json("prod_import_source");
            $table->string("prod_production_type");
            $table->integer("prod_cost");
            $table->integer("prod_available_quantity");
            $table->integer("prod_max_adver_cost");
            $table->string("prod_profitability")->nullable();
            $table->string("prod_profit")->nullable();
            $table->json("prod_image");
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
        Schema::dropIfExists('product_profile_infos');
    }
}
