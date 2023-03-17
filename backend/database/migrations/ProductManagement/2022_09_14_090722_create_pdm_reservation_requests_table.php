<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmReservationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_reservation_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('is_group');
            $table->text('products_note');
            $table->text('shipping_note');
            $table->enum('shipping_mehtod', ['air', 'land', 'sea']);
            $table->enum('status', ['pending', 'rejected', 'in_process', 'in_hold', 'not_possible', 'order_made', 'semi_purchased', 'recieved_in_warehouse', 'canceled',  'deleted']);


            $table->foreignUuid("company_id")->references("id")->on("companies");
            $table->foreignUuid("importing_city_id")->references("id")->on("states");
            $table->uuid("reason_id")->nullable();
            $table->foreign("reason_id")->references("id")->on("reasons");
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
        Schema::dropIfExists('pdm_reservation_requests');
    }
}
