<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmProductsRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_products_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('quantity');
            $table->integer('prograssive');
            $table->enum('status', ['in_process', 'oreder_made', 'recieved_in_warehouse', 'failed']);

            $table->foreignUuid("product_id")->references("id")->on("pdm_products");
            $table->foreignUuid("reservation_request_id")->references("id")->on("pdm_reservation_requests");

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
        Schema::dropIfExists('pdm_products_requests');
    }
}
