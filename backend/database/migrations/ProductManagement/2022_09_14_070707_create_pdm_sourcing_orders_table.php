<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdmSourcingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdm_sourcing_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('sourcing_type', ['quantity_check', 'supplier_check']);
            $table->boolean('is_group');
            $table->enum('required_shipping_method', ['air', 'land', 'sea']);
            $table->enum('status', ['pending', 'rejected', 'in_sourcing', 'in_hold', 'sourcing_failed', 'semi_found', 'found', 'canceled', 'deleted']);
            $table->text('description');
            $table->integer('progressive');


            $table->foreignUuid("searching_reason_id")->references("id")->on("reasons");
            $table->foreignUuid("company_id")->references("id")->on("companies");
            $table->foreignUuid("importing_city")->references("id")->on("states");
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
        Schema::dropIfExists('pdm_sourcing_orders');
    }
}
