<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuantityReservationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quantity_reservation_products', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->enum('status', ['in process', 'order made', "received in warehouse"])->default('order made');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreignUuid('quantity_reservation_id')->nullable()->constrained('quantity_reservation_requests_ssp')->nullOnDelete()->cascadeOnUpdate();
            $table->string('purchase_order_number')->nullable();
            $table->dateTime('arrival_time')->nullable();
            $table->dateTime('purchase_date')->nullable();
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
        Schema::dropIfExists('quantity_reservation_products');
    }
}
