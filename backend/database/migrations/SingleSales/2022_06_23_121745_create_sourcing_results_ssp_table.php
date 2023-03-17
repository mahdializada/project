<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourcingResultsSspTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sourcing_results_ssp', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("code")->unique();
            $table->foreignId('sourcing_request_product_id')->constrained('sourcing_request_products')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('cost'); // stores double with unclear precision
            $table->integer('supplier_id')->nullable();
            $table->enum('shipping_method', ['air', 'sea', 'ground', "other"])->nullable();
            $table->string('shipping_cost')->nullable();
            $table->dateTime('delivery_time')->nullable();
            $table->integer('available_quantities')->nullable();
            $table->text('import_note')->nullable();
            $table->text('import_restrictions')->nullable();
            $table->enum('quantity_model', ['whole sale', 'retail'])->nullable();
            $table->text('sourcing_note')->nullable();
            $table->text('product_note')->nullable();
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
        Schema::dropIfExists('sourcing_results_ssp');
    }
}
