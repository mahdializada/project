<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuantityReservationRequestsSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quantity_reservation_requests_ssp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("code")->unique();
            $table->text('products_note')->nullable();
            $table->enum('shipping_method', ['air', 'sea', 'ground', 'other'])->default('ground');
            $table->text('shipping_note')->nullable();
            $table->enum('status', ['pending', 'rejected', 'in process', 'in hold', 'not possible', 'order_made', 'semi_purchased', 'received', 'in warehouse', 'canceled', 'deleted'])->default('pending');
            $table->foreignUuid('importing_state_id')->nullable()->constrained('states')->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('quantity_reservation_requests_ssp');
    }
}
