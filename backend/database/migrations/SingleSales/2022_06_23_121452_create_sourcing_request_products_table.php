<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourcingRequestProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sourcing_request_products', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('sourcing_request_id')->nullable()->constrained("sourcing_requests_ssp")->cascadeOnUpdate()->nullOnDelete();
            $table->integer('product_id')->nullable();
            $table->unsignedInteger('approx_quantity')->nullable();
            $table->unsignedInteger('target_cost')->nullable();
            $table->text('note')->nullable();
            $table->json('reference')->nullable();
            $table->enum("status", ["found", "not found"])->nullable();
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
        Schema::dropIfExists('sourcing_request_products');
    }
}
